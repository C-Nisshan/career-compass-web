@extends('layouts.admin.master')

@section('title', 'User Management')

@section('content')
<div class="container user-table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="user-page-title">User Management</h1>
        <div class="user-action-header">
            <button class="btn btn-primary user-create-btn" data-bs-toggle="modal" data-bs-target="#userCreateUserModal"><i class="fas fa-user-plus"></i> Create User</button>
            <button class="btn btn-danger user-bulk-delete-btn" id="userBulkDeleteBtn" disabled>Delete Selected</button>
        </div>
    </div>
    <div class="user-search-container mb-4">
    <div class="input-group w-50">
            <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control user-search-input" id="userSearchInput" placeholder="Search users by email, role, or name...">
        </div>
    </div>
    <div id="userErrorMessage" class="user-error-message"></div>
    <table id="userUsersTable" class="table table-striped table-bordered user-table" style="width:100%">
        <thead>
            <tr>
                <th><input type="checkbox" id="userSelectAll" class="user-select-all"></th>
                <th>Profile</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- View User Details Modal -->
<div class="modal fade" id="userViewUserModal" tabindex="-1" aria-labelledby="userViewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content user-modal-content">
            <div class="modal-header user-modal-header">
                <h5 class="modal-title user-modal-title" id="userViewUserModalLabel">User Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body user-modal-body">
                <div id="userViewUserDetails">
                    <p><strong>ID:</strong> <span id="userViewUserId"></span></p>
                    <p><strong>Email:</strong> <span id="userViewUserEmail"></span></p>
                    <p><strong>Name:</strong> <span id="userViewUserName"></span></p>
                    <p><strong>Role:</strong> <span id="userViewUserRole"></span></p>
                    <p><strong>Active:</strong> <span id="userViewUserActive"></span></p>
                    <p><strong>Phone:</strong> <span id="userViewUserPhone"></span></p>
                    <p><strong>Address:</strong> <span id="userViewUserAddress"></span></p>
                    <p><strong>NIC Number:</strong> <span id="userViewUserNicNumber"></span></p>
                    <p><strong>Verification Status:</strong> <span id="userViewUserVerifiedStatus"></span></p>
                    <p><strong>Profile Picture:</strong> <span id="userViewUserProfilePicture"></span></p>
                    <p><strong>NIC Document:</strong> <span id="userViewUserNicDocument"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary user-btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('users.modals')
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .user-table-container {
        margin: 2rem 1rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        width: calc(100% - 270px);
        margin-left: 270px;
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    }
    @media (max-width: 768px) {
        .user-table-container {
            margin-left: 1rem;
            width: calc(100% - 2rem);
        }
    }
    .user-page-title {
        color: #1f2937;
        font-weight: 700;
        font-size: 2rem;
    }
    .user-action-header {
        display: flex;
        gap: 1rem;
    }
    .user-search-container {
        display: flex;
        justify-content: flex-start;
    }
    .user-search-input {
        border-radius: 0 0.5rem 0.5rem 0;
        border: 1px solid #d1d5db;
        transition: all 0.3s ease;
    }
    .user-search-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .user-table th {
        background: #4f46e5;
        color: white;
        font-weight: 600;
        padding: 1rem;
    }
    .user-table td {
        vertical-align: middle;
        padding: 1rem;
        transition: background 0.3s ease;
    }
    .user-table tr:hover td {
        background: #f0f4ff;
    }
    .user-action-buttons button {
        margin-right: 0.5rem;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    .user-error-message {
        display: none;
        color: #ef4444;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    .user-error-message.loading {
        color: #4f46e5;
    }
    .user-tooltip-card {
        position: absolute;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        padding: 1rem;
        z-index: 1000;
        max-width: 400px;
        font-size: 0.875rem;
        color: #1f2937;
        border: 2px solid #4f46e5;
    }
    .user-tooltip-card p {
        margin: 0.5rem 0;
    }
    .user-profile-img {
        border-radius: 50%;
        object-fit: cover;
    }
    .user-clear-search {
        border-radius: 0 0.5rem 0.5rem 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('assets/js/users.js') }}"></script>
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#userUsersTable')) {
            $('#userErrorMessage').text('Failed to initialize DataTables. Check console for errors.').show();
        }
    });
</script>
@endpush