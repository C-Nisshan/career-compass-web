<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management Modals</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        .user-modal-content {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #ffffff, #f0f4ff);
        }
        .user-modal-header {
            background: #4f46e5;
            color: white;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 1.5rem;
        }
        .user-modal-title {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .user-modal-body {
            padding: 2rem;
        }
        .user-form-label {
            font-weight: 500;
            color: #1f2937;
        }
        .user-form-control, .user-form-select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            transition: all 0.3s ease;
        }
        .user-form-control:focus, .user-form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        .user-btn-primary {
            background: #4f46e5;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .user-btn-primary:hover {
            background: #4338ca;
        }
        .user-btn-secondary {
            background: #6b7280;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .user-btn-danger {
            background: #ef4444;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .user-error-message {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translateY(-50px);
        }
        .modal.show .modal-dialog {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Create User Modal -->
    <div class="modal fade" id="userCreateUserModal" tabindex="-1" aria-labelledby="userCreateUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content user-modal-content">
                <div class="modal-header user-modal-header">
                    <h5 class="modal-title user-modal-title" id="userCreateUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body user-modal-body">
                    <form id="userCreateUserForm" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="userCreateEmail" class="form-label user-form-label">Email <span class="text-red-500">*</span></label>
                            <input type="email" class="form-control user-form-control" id="userCreateEmail" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label for="userCreatePassword" class="form-label user-form-label">Password <span class="text-red-500">*</span></label>
                            <input type="password" class="form-control user-form-control" id="userCreatePassword" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label for="userCreatePasswordConfirmation" class="form-label user-form-label">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" class="form-control user-form-control" id="userCreatePasswordConfirmation" name="password_confirmation" required>
                        </div>
                        <div class="mb-4">
                            <label for="userCreateRole" class="form-label user-form-label">Role <span class="text-red-500">*</span></label>
                            <select class="form-select user-form-select" id="userCreateRole" name="role" required>
                                <option value="" disabled selected>Select a role</option>
                                <option value="admin">Admin</option>
                                <option value="student">Student</option>
                                <option value="provider">Provider</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="userCreateIsActive" class="form-label user-form-label">Active</label>
                            <select class="form-select user-form-select" id="userCreateIsActive" name="is_active">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="userCreateFirstName" class="form-label user-form-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" class="form-control user-form-control" id="userCreateFirstName" name="first_name" required>
                        </div>
                        <div class="mb-4">
                            <label for="userCreateLastName" class="form-label user-form-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" class="form-control user-form-control" id="userCreateLastName" name="last_name" required>
                        </div>
                        <div class="mb-4">
                            <label for="userCreatePhone" class="form-label user-form-label">Phone</label>
                            <input type="text" class="form-control user-form-control" id="userCreatePhone" name="phone">
                        </div>
                        <div class="mb-4">
                            <label for="userCreateAddress" class="form-label user-form-label">Address</label>
                            <input type="text" class="form-control user-form-control" id="userCreateAddress" name="address">
                        </div>
                        <div class="mb-4">
                            <label for="userCreateNicNumber" class="form-label user-form-label">NIC Number</label>
                            <input type="text" class="form-control user-form-control" id="userCreateNicNumber" name="nic_number">
                        </div>
                        <div class="mb-4">
                            <label for="userCreateProfilePicture" class="form-label user-form-label">Profile Picture (JPEG/PNG)</label>
                            <input type="file" name="profile_picture" id="userCreateProfilePicture" accept="image/jpeg,image/png">
                        </div>
                        <div class="mb-4">
                            <label for="userCreateNicDocument" class="form-label user-form-label">NIC Document (PDF/JPEG/PNG)</label>
                            <input type="file" name="nic_document" id="userCreateNicDocument" accept="application/pdf,image/jpeg,image/png">
                        </div>
                        <div id="userCreateError" class="text-danger user-error-message"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary user-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary user-btn-primary" id="userSaveNewUserBtn">Create User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="userEditUserModal" tabindex="-1" aria-labelledby="userEditUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content user-modal-content">
                <div class="modal-header user-modal-header">
                    <h5 class="modal-title user-modal-title" id="userEditUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body user-modal-body">
                    <form id="userEditUserForm" enctype="multipart/form-data">
                        <input type="hidden" id="userEditUserId" name="id">
                        <div class="mb-4">
                            <label for="userEditEmail" class="form-label user-form-label">Email</label>
                            <input type="email" class="form-control user-form-control" id="userEditEmail" name="email">
                        </div>
                        <div class="mb-4">
                            <label for="userEditPassword" class="form-label user-form-label">Password (leave blank to keep unchanged)</label>
                            <input type="password" class="form-control user-form-control" id="userEditPassword" name="password">
                        </div>
                        <div class="mb-4">
                            <label for="userEditPasswordConfirmation" class="form-label user-form-label">Confirm Password</label>
                            <input type="password" class="form-control user-form-control" id="userEditPasswordConfirmation" name="password_confirmation">
                        </div>
                        <div class="mb-4">
                            <label for="userEditRole" class="form-label user-form-label">Role</label>
                            <select class="form-select user-form-select" id="userEditRole" name="role">
                                <option value="admin">Admin</option>
                                <option value="student">Student</option>
                                <option value="provider">Provider</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="userEditIsActive" class="form-label user-form-label">Active</label>
                            <select class="form-select user-form-select" id="userEditIsActive" name="is_active">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="userEditFirstName" class="form-label user-form-label">First Name</label>
                            <input type="text" class="form-control user-form-control" id="userEditFirstName" name="first_name">
                        </div>
                        <div class="mb-4">
                            <label for="userEditLastName" class="form-label user-form-label">Last Name</label>
                            <input type="text" class="form-control user-form-control" id="userEditLastName" name="last_name">
                        </div>
                        <div class="mb-4">
                            <label for="userEditPhone" class="form-label user-form-label">Phone</label>
                            <input type="text" class="form-control user-form-control" id="userEditPhone" name="phone">
                        </div>
                        <div class="mb-4">
                            <label for="userEditAddress" class="form-label user-form-label">Address</label>
                            <input type="text" class="form-control user-form-control" id="userEditAddress" name="address">
                        </div>
                        <div class="mb-4">
                            <label for="userEditNicNumber" class="form-label user-form-label">NIC Number</label>
                            <input type="text" class="form-control user-form-control" id="userEditNicNumber" name="nic_number">
                        </div>
                        <div class="mb-4">
                            <label for="userEditProfilePicture" class="form-label user-form-label">Profile Picture (JPEG/PNG)</label>
                            <input type="file" name="profile_picture" id="userEditProfilePicture" accept="image/jpeg,image/png">
                        </div>
                        <div class="mb-4">
                            <label for="userEditNicDocument" class="form-label user-form-label">NIC Document (PDF/JPEG/PNG)</label>
                            <input type="file" name="nic_document" id="userEditNicDocument" accept="application/pdf,image/jpeg,image/png">
                        </div>
                        <div class="mb-4">
                            <label for="userEditVerifiedStatus" class="form-label user-form-label">Verification Status</label>
                            <select class="form-select user-form-select" id="userEditVerifiedStatus" name="verified_status">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div id="userEditError" class="text-danger user-error-message"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary user-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary user-btn-primary" id="userSaveUserBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="userDeleteUserModal" tabindex="-1" aria-labelledby="userDeleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content user-modal-content">
                <div class="modal-header user-modal-header">
                    <h5 class="modal-title user-modal-title" id="userDeleteUserModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body user-modal-body">
                    <p class="text-gray-700">Are you sure you want to delete this user? This action cannot be undone.</p>
                    <input type="hidden" id="userDeleteUserId">
                    <div id="userDeleteError" class="text-danger user-error-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary user-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger user-btn-danger" id="userConfirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div class="modal fade" id="userBulkDeleteModal" tabindex="-1" aria-labelledby="userBulkDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content user-modal-content">
                <div class="modal-header user-modal-header">
                    <h5 class="modal-title user-modal-title" id="userBulkDeleteModalLabel">Confirm Bulk Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body user-modal-body">
                    <p class="text-gray-700">Are you sure you want to delete the selected users? This action cannot be undone.</p>
                    <div id="userBulkDeleteError" class="text-danger user-error-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary user-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger user-btn-danger" id="userConfirmBulkDeleteBtn">Delete Selected</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/imageCompressor.js"></script>
</body>
</html>