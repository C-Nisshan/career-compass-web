$(document).ready(function () {
    // Initialize DataTables
    let table = $('#userUsersTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: '/api/users',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
                let params = {
                    start: d.start || 0,
                    length: d.length || 10,
                    search: $('#userSearchInput').val().trim(),  
                    draw: d.draw || 1
                };
                console.log('Sending AJAX request with params:', params);
                return params;
            },
            beforeSend: function () {
                $('#userErrorMessage').text('Loading...').addClass('loading').show();
            },
            complete: function () {
                $('#userErrorMessage').removeClass('loading').hide();
            },
            error: function (xhr, error, thrown) {
                console.error('DataTables AJAX error:', { status: xhr.status, response: xhr.responseJSON, error, thrown });
                if (xhr.status === 401) {
                    alert('Unauthorized. Please log in.');
                    window.location.href = '/login';
                } else {
                    $('#userErrorMessage').text('Failed to load users. Please try again.').show();
                }
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `<input type="checkbox" class="user-select-row" data-id="${data.id}">`;
                }
            },
            {
                data: null,
                searchable: false,
                render: function (data) {
                    console.log('Rendering profile picture for user:', data);
                    const path = data.profile?.profile_picture_path;
                    const isImage = path && (path.endsWith('.jpg') || path.endsWith('.jpeg') || path.endsWith('.png'));
                    if (isImage) {
                        return `<img src="/storage/${path}" alt="Profile" class="user-profile-img" width="40" height="40" onerror="this.parentNode.innerHTML='N/A'">`;
                    }
                    return 'N/A';
                }
            },
            {
                data: null,
                render: function (data) {
                    return (data.profile ? `${data.profile.first_name || ''} ${data.profile.last_name || ''}`.trim() : 'N/A');
                }
            },
            { data: 'email' },
            { data: 'role' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <div class="user-action-buttons">
                            <button class="btn btn-sm btn-primary user-view-btn" data-id="${data.id}">View</button>
                            <button class="btn btn-sm btn-warning user-edit-btn" data-id="${data.id}">Edit</button>
                            <button class="btn btn-sm btn-danger user-delete-btn" data-id="${data.id}">Delete</button>
                        </div>
                    `;
                }
            }
        ],
        dom: 'lBrtip',
        lengthMenu: [10, 25, 50, 100],
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        select: {
            style: 'multi',
            selector: 'td:first-child .user-select-row'
        },
        rowCallback: function (row, data) {
            $(row).hover(
                function () {
                    const offset = $(this).offset();
                    const tooltip = $(`
                        <div class="user-tooltip-card">
                            <p><strong>ID:</strong> ${data.id || 'N/A'}</p>
                            <p><strong>Phone:</strong> ${data.profile?.phone || 'N/A'}</p>
                            <p><strong>Address:</strong> ${data.profile?.address || 'N/A'}</p>
                            <p><strong>NIC Number:</strong> ${data.profile?.nic_number || 'N/A'}</p>
                            <p><strong>Verification Status:</strong> ${data.profile?.verified_status || 'N/A'}</p>
                        </div>
                    `);
                    tooltip.css({
                        top: offset.top + $(this).outerHeight(),
                        left: offset.left
                    });
                    $('body').append(tooltip);
                },
                function () {
                    $('.user-tooltip-card').remove();
                }
            );
            $(row).on('dblclick', function () {
                populateViewModal(data);
            });
        }
    });

    // Debounce search input
    let searchTimeout;
    $('#userSearchInput').on('input', function () {
        let value = $(this).val().trim();
        console.log('Search input value:', value);
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            console.log('Triggering DataTables search with:', value);
            table.search(value).draw();
        }, 500);
    });

    // Select all checkbox
    $('#userSelectAll').on('change', function () {
        const checked = this.checked;
        $('.user-select-row').prop('checked', checked);
        table.rows().select(checked);
        $('#userBulkDeleteBtn').prop('disabled', !table.rows({ selected: true }).data().length);
    });

    // Row checkbox change
    $('#userUsersTable').on('change', '.user-select-row', function () {
        const row = table.row($(this).closest('tr'));
        if (this.checked) {
            row.select();
        } else {
            row.deselect();
        }
        $('#userBulkDeleteBtn').prop('disabled', !table.rows({ selected: true }).data().length);
    });

    // Bulk delete button
    $('#userBulkDeleteBtn').click(function () {
        let selected = table.rows({ selected: true }).data().toArray();
        if (selected.length === 0) {
            alert('Please select at least one user.');
            return;
        }
        $('#userBulkDeleteModal').modal('show');
    });

    // Clear create modal when shown
    $('#userCreateUserModal').on('show.bs.modal', function () {
        $('#userCreateUserForm')[0].reset();
        $('#userCreateError').text('');
    });

    // Save new user
    $('#userSaveNewUserBtn').click(async function () {
        const form = $('#userCreateUserForm')[0];
        const formData = new FormData();

        for (let element of form.elements) {
            if (element.name && element.type !== 'file') {
                formData.append(element.name, element.value);
            }
        }

        const profilePic = $('#userCreateProfilePicture')[0].files[0];
        if (profilePic) {
            console.log('Profile picture:', {
                name: profilePic.name,
                type: profilePic.type,
                size: profilePic.size
            });
            formData.append('profile_picture', profilePic);
        }

        const nicDoc = $('#userCreateNicDocument')[0].files[0];
        if (nicDoc) {
            console.log('NIC document:', {
                name: nicDoc.name,
                type: nicDoc.type,
                size: nicDoc.size
            });
            formData.append('nic_document', nicDoc);
        }

        $.ajax({
            url: '/api/users',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#userCreateUserModal').modal('hide');
                $('#userCreateUserForm')[0].reset();
                table.ajax.reload(null, false);
                alert('User created successfully');
            },
            error: function (xhr) {
                console.error('Create user error:', xhr);
                $('#userCreateError').text(xhr.responseJSON.errors
                    ? Object.values(xhr.responseJSON.errors).join(', ')
                    : 'Error creating user');
            }
        });
    });

    // View button click
    $('#userUsersTable').on('click', '.user-view-btn', function () {
        let id = $(this).data('id');
        $.ajax({
            url: '/api/users/' + id,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                populateViewModal(data);
            },
            error: function (xhr) {
                alert('Error fetching user data');
            }
        });
    });

    // Edit button click
    $('#userUsersTable').on('click', '.user-edit-btn', function () {
        let id = $(this).data('id');
        $.ajax({
            url: '/api/users/' + id,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $('#userEditUserId').val(data.id);
                $('#userEditEmail').val(data.email || '');
                $('#userEditRole').val(data.role || '');
                $('#userEditIsActive').val(data.is_active ? '1' : '0');
                $('#userEditFirstName').val(data.profile?.first_name || '');
                $('#userEditLastName').val(data.profile?.last_name || '');
                $('#userEditPhone').val(data.profile?.phone || '');
                $('#userEditAddress').val(data.profile?.address || '');
                $('#userEditNicNumber').val(data.profile?.nic_number || '');
                $('#userEditProfilePicture').val('');
                $('#userEditNicDocument').val('');
                $('#userEditVerifiedStatus').val(data.profile?.verified_status || 'pending');
                $('#userEditError').text('');
                $('#userEditUserModal').modal('show');
            },
            error: function (xhr) {
                $('#userEditError').text(xhr.responseJSON.message || 'Error fetching user data');
            }
        });
    });

    // Save user changes
    $('#userSaveUserBtn').click(async function () {
        const form = $('#userEditUserForm')[0];
        const formData = new FormData();
        const id = $('#userEditUserId').val();

        for (let element of form.elements) {
            if (element.name && element.type !== 'file') {
                formData.append(element.name, element.value);
            }
        }

        const profilePic = $('#userEditProfilePicture')[0].files[0];
        if (profilePic) {
            console.log('Profile picture:', {
                name: profilePic.name,
                type: profilePic.type,
                size: profilePic.size
            });
            formData.append('profile_picture', profilePic);
        }

        const nicDoc = $('#userEditNicDocument')[0].files[0];
        if (nicDoc) {
            console.log('NIC document:', {
                name: nicDoc.name,
                type: nicDoc.type,
                size: nicDoc.size
            });
            formData.append('nic_document', nicDoc);
        }

        $.ajax({
            url: '/api/users/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function (response) {
                $('#userEditUserModal').modal('hide');
                table.ajax.reload();
                alert('User updated successfully');
            },
            error: function (xhr) {
                $('#userEditError').text(xhr.responseJSON.errors
                    ? Object.values(xhr.responseJSON.errors).join(', ')
                    : 'Error updating user');
            }
        });
    });

    // Delete button click
    $('#userUsersTable').on('click', '.user-delete-btn', function () {
        let id = $(this).data('id');
        $('#userDeleteUserId').val(id);
        $('#userDeleteError').text('');
        $('#userDeleteUserModal').modal('show');
    });

    // Confirm single delete
    $('#userConfirmDeleteBtn').click(function () {
        let id = $('#userDeleteUserId').val();
        $.ajax({
            url: '/api/users/' + id,
            type: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                $('#userDeleteUserModal').modal('hide');
                table.ajax.reload();
                alert('User deleted successfully');
            },
            error: function (xhr) {
                $('#userDeleteError').text(xhr.responseJSON.message || 'Error deleting user');
            }
        });
    });

    // Confirm bulk delete
    $('#userConfirmBulkDeleteBtn').click(function () {
        let selected = table.rows({ selected: true }).data().toArray();
        let ids = selected.map(row => row.id);
        if (ids.length === 0) return;
        $.ajax({
            url: '/api/users/bulk-delete',
            type: 'POST',
            data: JSON.stringify({ ids: ids }),
            contentType: 'application/json',
            headers: {
                'Authorization': 'Bearer ' + getCookie('token'),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#userBulkDeleteModal').modal('hide');
                table.ajax.reload();
                alert(response.message);
            },
            error: function (xhr) {
                $('#userBulkDeleteError').text(xhr.responseJSON.message || 'Error deleting users');
            }
        });
    });

    // Populate view modal
    function populateViewModal(data) {
        $('#userViewUserId').text(data.id || 'N/A');
        $('#userViewUserEmail').text(data.email || 'N/A');
        $('#userViewUserName').text(data.profile ? `${data.profile.first_name || ''} ${data.profile.last_name || ''}`.trim() : 'N/A');
        $('#userViewUserRole').text(data.role || 'N/A');
        $('#userViewUserActive').text(data.is_active ? 'Yes' : 'No');
        $('#userViewUserPhone').text(data.profile?.phone || 'N/A');
        $('#userViewUserAddress').text(data.profile?.address || 'N/A');
        $('#userViewUserNicNumber').text(data.profile?.nic_number || 'N/A');
        $('#userViewUserVerifiedStatus').text(data.profile?.verified_status || 'N/A');
        const path = data.profile?.profile_picture_path;
        const isImage = path && (path.endsWith('.jpg') || path.endsWith('.jpeg') || path.endsWith('.png'));
        if (isImage) {
            $('#userViewUserProfilePicture').html(`<a href="/storage/${path}" target="_blank"><img src="/storage/${path}" width="100" alt="Profile Picture" onerror="this.parentNode.innerHTML='N/A'"></a>`);
        } else {
            $('#userViewUserProfilePicture').html('N/A');
        }
        $('#userViewUserNicDocument').html(data.verificationDocuments?.length ? `<a href="/storage/${data.verificationDocuments[0].file_path}" target="_blank">View</a>` : 'N/A');
        $('#userViewUserModal').modal('show');
    }

    // Get cookie function
    function getCookie(name) {
        let value = `; ${document.cookie}`;
        let parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
});