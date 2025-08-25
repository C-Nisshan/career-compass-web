@extends('layouts.app')

@section('content')
<link href="{{ asset('css/mentor-approvals.css') }}" rel="stylesheet">

<div class="mentor-approvals-container">
    <div class="mentor-approvals-content">
        <h1 class="mentor-approvals-title">Mentor Management Dashboard</h1>
        
        <div class="mentor-approvals-card">
            <div class="mentor-approvals-header">
                <h2 class="mentor-approvals-subtitle">Mentor Registrations</h2>
                <div class="mentor-approvals-filter">
                    <label for="mentor-approvals-status-filter">Filter by Status:</label>
                    <select id="mentor-approvals-status-filter">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div id="mentor-approvals-loading" class="hidden">Loading...</div>
            <div id="mentor-approvals-error" class="hidden"></div>
            
            <div class="mentor-approvals-table-container">
                <table class="mentor-approvals-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Profession</th>
                            <th>Industry</th>
                            <th>Expertise</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mentor-approvals-table-body">
                        <!-- Populated dynamically via JavaScript -->
                    </tbody>
                </table>

                <!-- Mentor Detail Modal -->
                <div id="mentor-detail-modal" class="mentor-modal hidden">
                    <div class="mentor-modal-content">
                        <span id="mentor-modal-close" class="mentor-modal-close">&times;</span>
                        <div id="mentor-modal-body">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired'); // Debug: Confirm DOMContentLoaded event

    const loading = document.getElementById('mentor-approvals-loading');
    const error = document.getElementById('mentor-approvals-error');
    const tableBody = document.getElementById('mentor-approvals-table-body');
    const statusFilter = document.getElementById('mentor-approvals-status-filter');

    // Debug: Check if the statusFilter element exists
    if (!statusFilter) {
        console.error('Status filter element not found!');
        error.textContent = 'Status filter dropdown not found in the DOM.';
        error.classList.remove('hidden');
        return;
    } else {
        console.log('Status filter element found:', statusFilter); // Debug: Confirm element is found
    }

    let mentorData = [];

    async function fetchMentors(status = '') {
        console.log('Fetching mentors with status:', status); // Debug: Log the status
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        tableBody.innerHTML = '';

        try {
            const url = status ? `/api/admin/mentors?status=${encodeURIComponent(status)}` : '/api/admin/mentors';
            console.log('API URL:', url); // Debug: Log the API URL
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('API Response:', result); // Debug: Log the response

            if (response.ok && result.success) {
                mentorData = result.data;
                if (mentorData.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="mentor-approvals-no-data">No mentors found.</td></tr>';
                } else {
                    mentorData.forEach(mentor => {
                        const expertise = Array.isArray(mentor.mentor_profile?.areas_of_expertise)
                            ? mentor.mentor_profile.areas_of_expertise.join(', ')
                            : mentor.mentor_profile?.areas_of_expertise || 'N/A';
                        const statusClass = `mentor-approvals-status-${mentor.status}`;
                        let actions = '';
                        if (mentor.status === 'pending') {
                            actions = `
                                <button onclick="handleAction('${mentor.uuid}', 'approve')" class="mentor-approvals-action-btn mentor-approvals-approve-btn" title="Approve"></button>
                                <button onclick="handleAction('${mentor.uuid}', 'reject')" class="mentor-approvals-action-btn mentor-approvals-reject-btn" title="Reject"></button>
                            `;
                        } else if (mentor.status === 'approved') {
                            actions = `
                                <button onclick="handleAction('${mentor.uuid}', 'pending')" class="mentor-approvals-action-btn mentor-approvals-pending-btn" title="Set Pending"></button>
                                <button onclick="handleAction('${mentor.uuid}', 'reject')" class="mentor-approvals-action-btn mentor-approvals-reject-btn" title="Reject"></button>
                            `;
                        } else if (mentor.status === 'rejected') {
                            actions = `
                                <button onclick="handleAction('${mentor.uuid}', 'pending')" class="mentor-approvals-action-btn mentor-approvals-pending-btn" title="Set Pending"></button>
                                <button onclick="handleAction('${mentor.uuid}', 'approve')" class="mentor-approvals-action-btn mentor-approvals-approve-btn" title="Approve"></button>
                            `;
                        }

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${mentor.first_name} ${mentor.last_name}</td>
                            <td>${mentor.email}</td>
                            <td>${mentor.mentor_profile?.profession_title || 'N/A'}</td>
                            <td>${mentor.mentor_profile?.industry || 'N/A'}</td>
                            <td>${expertise}</td>
                            <td class="${statusClass}">${mentor.status.charAt(0).toUpperCase() + mentor.status.slice(1)}</td>
                            <td>
                                <div class="mentor-approvals-actions-wrapper">
                                    ${actions}
                                    <button class="mentor-approvals-action-btn mentor-approvals-view-btn" data-uuid="${mentor.uuid}" title="View Details"></button>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }
            } else {
                error.textContent = result.message || 'Failed to load mentors. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch mentors. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    window.handleAction = async function(uuid, action) {
        if (!confirm(`Are you sure you want to set this mentor to ${action}?`)) return;

        try {
            const response = await fetch(`/api/admin/mentors/${action}/${uuid}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Action Response:', result);

            if (response.ok && result.success) {
                fetchMentors(statusFilter.value);
                alert(result.message);
            } else {
                error.textContent = result.message || `Failed to set mentor to ${action}. Please try again.`;
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Action error:', err);
            error.textContent = 'Unable to process action. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    };

    // View Details modal open
    function openMentorModal(mentor) {
        const modal = document.getElementById('mentor-detail-modal');
        const modalBody = document.getElementById('mentor-modal-body');

        const expertise = Array.isArray(mentor.mentor_profile?.areas_of_expertise)
            ? mentor.mentor_profile.areas_of_expertise.join(', ')
            : mentor.mentor_profile?.areas_of_expertise || 'N/A';

        modalBody.innerHTML = `
            <h2>${mentor.first_name} ${mentor.last_name}</h2>
            <p><strong>Email:</strong> ${mentor.email}</p>
            <p><strong>Profession:</strong> ${mentor.mentor_profile?.profession_title || 'N/A'}</p>
            <p><strong>Industry:</strong> ${mentor.mentor_profile?.industry || 'N/A'}</p>
            <p><strong>Experience:</strong> ${mentor.mentor_profile?.experience_years || 'N/A'} years</p>
            <p><strong>Expertise:</strong> ${expertise}</p>
            <p><strong>Bio:</strong> ${mentor.mentor_profile?.bio || 'N/A'}</p>
            <p><strong>LinkedIn:</strong> ${mentor.mentor_profile?.linkedin_url ? `<a href="${mentor.mentor_profile.linkedin_url}" target="_blank">View Profile</a>` : 'N/A'}</p>
            <p><strong>Portfolio:</strong> ${mentor.mentor_profile?.portfolio_url ? `<a href="${mentor.mentor_profile.portfolio_url}" target="_blank">View Portfolio</a>` : 'N/A'}</p>
            <p><strong>Availability:</strong> ${mentor.mentor_profile?.availability || 'N/A'}</p>
        `;

        modal.classList.remove('hidden');
    }

    // Close modal
    document.getElementById('mentor-modal-close').addEventListener('click', () => {
        document.getElementById('mentor-detail-modal').classList.add('hidden');
    });

    // Delegate click event for "View Details" buttons
    tableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('mentor-view-btn')) {
            const uuid = e.target.getAttribute('data-uuid');
            const mentor = mentorData.find(m => m.uuid === uuid);
            if (mentor) openMentorModal(mentor);
        }
    });

    // Status filter change with explicit debugging
    statusFilter.addEventListener('change', () => {
        console.log('Dropdown changed! Selected value:', statusFilter.value); // Debug: Confirm change event
        fetchMentors(statusFilter.value);
    });

    // Test the dropdown directly
    statusFilter.addEventListener('click', () => {
        console.log('Dropdown clicked! Current value:', statusFilter.value); // Debug: Confirm click event
    });

    fetchMentors(); // Initial load
});
</script>
@endsection
