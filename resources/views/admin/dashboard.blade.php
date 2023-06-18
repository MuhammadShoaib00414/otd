@extends('admin.layout')

@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
@endsection

@push('stylestack')
<style>
  .admin-menu-card-body {
    padding: 0.5em 0.5em 0 0.5em;
  }

  .admin-menu-item {
    display: block;
    margin-bottom: 0.5em;
    padding: 0.5em;
    border-radius: 0.5em;
  }

  .admin-menu-item:hover {
    text-decoration: none;
    background-color: #eee;
  }

  .admin-menu-item-column {
    flex: 0 0 100%;
    max-width: 100%;
  }

  @media(min-width: 800px) {
    .admin-menu-item-column {
      flex: 0 0 50%;
      max-width: 50%;
    }
  }

  @media(min-width: 1100px) {
    .admin-menu-item-column {
      flex: 0 0 33.33333333%;
      max-width: 33.33333333%;
    }
  }

  .admin-menu-item p {
    margin-bottom: 0;
    font-weight: bold;
  }

  .admin-menu-item span {
    color: #868686;
  }
</style>
@endpush

@section('page-content')
@component('admin.partials.breadcrumbs', ['links' => []])
@endcomponent

<div class="mb-5">
  <div class="row">
    <div class="col-12 col-md-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Users</h5>
        <p class="mb-0 text-muted">{{ App\User::where('is_enabled', '=', 1)->where('is_hidden', '=', 0)->whereNull('deleted_at')->count() }} active</p>
      </div>
      <div class="card">
        <div class="card-body admin-menu-card-body">
          <a href="/admin/users" class="admin-menu-item">
            <p>All Users</p>
            <span>View and manage users</span>
          </a>
          <a href="/admin/users/invites/create" class="admin-menu-item">
            <p>Invite Users</p>
            <span>Send invitations to register via email</span>
          </a>
          <a href="/admin/users/invites?show=invited" class="admin-menu-item">
            <p>Pending Invites</p>
            <span>Manage and resend outstanding invitations</span>
          </a>
          <a href="/admin/users/invites/bulk-resend" class="admin-menu-item">
            <p>Bulk Resend Invites</p>
            <span>Resend platform invitations in bulk</span>
          </a>
          <a href="/admin/users/bulk-delete" class="admin-menu-item">
            <p>Bulk Delete Users</p>
            <span>Delete users in bulk</span>
          </a>
          <a href="/admin/users/invites/cleanup" class="admin-menu-item">
            <p>Invitation Cleanup Tool</p>
            <span>Delete duplicates or old invitations</span>
          </a>
          <a href="/admin/reported" class="admin-menu-item">
            <p>Reported Activity</p>
            <span>View all reported and resolved posts</span>
          </a>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Groups</h5>
        <p class="mb-0 text-muted">{{ App\Group::count() }} groups</p>
      </div>
      <div class="card">
        <div class="card-body admin-menu-card-body">
          <a href="/admin/groups" class="admin-menu-item">
            <p>All Groups</p>
            <span>View and manage groups</span>
          </a>
          <a href="/admin/groups/create" class="admin-menu-item">
            <p>New Group</p>
            <span>Create a new group and configure settings</span>
          </a>
          <a href="/admin/groups/sort" class="admin-menu-item">
            <p>Sort Groups</p>
            <span>Change the order groups show up</span>
          </a>
          <a href="/admin/groups/bulk-settings" class="admin-menu-item">
            <p>Bulk Group Settings Edit</p>
            <span>Edit all group settings on one page</span>
          </a>
          <a href="/admin/groups/configuration" class="admin-menu-item">
            <p>Groups Configuration</p>
            <span>Customize how groups function on the platform</span>
          </a>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Tools</h5>
      </div>
      <div class="card">
        <div class="card-body admin-menu-card-body">
          <a href="/admin/emails/campaigns" class="admin-menu-item">
            <p>Email Campaigns</p>
            <span>Create, manage, and send mass emails to users</span>
          </a>
          <a href="/admin/segments" class="admin-menu-item">
            <p>Reports</p>
            <span>Monitor and export platform usage</span>
          </a>
          <a href="/admin/posts" class="admin-menu-item">
            <p>Manage Posts</p>
            <span>View and manage all posts on the platform</span>
          </a>
          <a href="/admin/events" class="admin-menu-item">
            <p>Manage Event</p>
            <span>View and manage all events on the platform</span>
          </a>
          <a href="/admin/content" class="admin-menu-item">
            <p>Manage Content</p>
            <span>View and manage content post</span>
          </a>
          @if(getsetting('is_ideations_enabled'))
          <a href="/admin/ideations" class="admin-menu-item">
            <p>Manage Ideations</p>
            <span>View and manage Ideations</span>
          </a>
          @endif
          <a href="/admin/budgets" class="admin-menu-item">
            <p>Budgets</p>
            <span>Create and edit individual group budgets</span>
          </a>
          @if($is_pages->value== 1)
          <a href="/admin/pages" class="admin-menu-item ">
            <p>Pages</p>
            <span>Create and customize pages </span>
          </a>
          @endif
        </div>
      </div>
    </div> 
  </div>
  <div class="mt-5 d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">System Settings</h5>
  </div>
  <div class="card">
    <div class="card-body admin-menu-card-body d-flex flex-wrap">
      <a href="/admin/system/platform-branding" class="admin-menu-item admin-menu-item-column">
        <p>Platform Branding</p>
        <span>Manage platform name, colors and theme</span>
      </a>
      <a href="/admin/system/feature-settings" class="admin-menu-item admin-menu-item-column">
        <p>Feature Settings</p>
        <span>Change the public name of platform features</span>
      </a>
      <a href="/admin/tutorials" class="admin-menu-item">
        <p>Tutorials</p>
        <span>Learn how  system works</span>
      </a>
      @if(getsetting('is_stripe_enabled'))
      <a href="/admin/system/payment-configuration" class="admin-menu-item admin-menu-item-column">
        <p>Payment Configuration</p>
        <span>Input your Stripe keys</span>
      </a>
      @endif
      <a href="/admin/system/dashboard-settings" class="admin-menu-item admin-menu-item-column">
        <p>Dashboard Settings</p>
        <span>Update graphics and dashboard settings</span>
      </a>
      <a href="/admin/points" class="admin-menu-item admin-menu-item-column">
        <p>Points</p>
        <span>Turn on/off, edit points allocations</span>
      </a>
      @if(getsetting('is_gdpr_enabled'))
      <a href="/admin/system/gdpr-settings" class="admin-menu-item admin-menu-item-column">
        <p>GDPR Settings</p>
        <span>Settings related to GDPR compliance</span>
      </a>
      @endif
      <a href="/admin/system/feed-post-settings" class="admin-menu-item admin-menu-item-column">
        <p>Feed &amp; Post Settings</p>
        <span>Manage platform-wide post settings</span>
      </a>
      <a href="/admin/mobile" class="admin-menu-item admin-menu-item-column">
        <p>Mobile App Settings</p>
        <span>Customize bottom navbar on mobile</span>
      </a>
      <a href="/admin/settings" class="admin-menu-item">
        <p>Manage Settings</p>
        <span>Settings related to website</span>
      </a>
     
    
      @if(request()->user()->is_super_admin)
      <a href="/admin/instance-settings" class="admin-menu-item">
        <p>Instance Settings</p>
        <span>Customize Instance Settings</span>
      </a>
      @endif


      <a href="/admin/dashboard-settings" class="admin-menu-item admin-menu-item-column">
        <p>Admin Dashboard Settings</p>
        <span>Manage dashboard button colors </span>
      </a>

      <a href="/admin/terms-and-conditions" class="admin-menu-item admin-menu-item-column">
        <p>Terms and Conditions</p>
        <span>Edit and customize terms and conditions</span>
      </a>
    </div>
  </div>
  <div class="mt-5 d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">User &amp; Profile</h5>
  </div>
  <div class="card">
    <div class="card-body admin-menu-card-body d-flex flex-wrap">
      <a href="/admin/system/homepage-login" class="admin-menu-item admin-menu-item-column">
        <p>Homepage &amp; Login Settings</p>
        <span>Configure homepage, login, and registration page settings</span>
      </a>
      <a href="/admin/registration" class="admin-menu-item admin-menu-item-column">
        <p>Registration Pages</p>
        <span>Setup registration flows, and optional payments for access</span>
      </a>
      <a href="/admin/system/profile-options" class="admin-menu-item admin-menu-item-column">
        <p>Profile Options</p>
        <span>Configure what users can fill in on their profile</span>
      </a>
      <a href="/admin/system/onboarding" class="admin-menu-item admin-menu-item-column">
        <p>Onboarding Configuration</p>
        <span>Customize platform onboarding, steps, questions, prompts</span>
      </a>
      <a href="/admin/categories" class="admin-menu-item admin-menu-item-column">
        <p>Profile Categories</p>
        <span>Create, Edit, Delete user-selectable profile categories</span>
      </a>
      <a href="/admin/questions" class="admin-menu-item admin-menu-item-column">
        <p>Profile Questions</p>
        <span>Define custom questions and inputs for users profiles</span>
      </a>
      <a href="/admin/badges" class="admin-menu-item admin-menu-item-column">
        <p>Badges</p>
        <span>Customize badges users may acquire for platform use</span>
      </a>
    </div>
  </div>

  <div class="mt-5 d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Platform Communications</h5>
  </div>
  <div class="card">
    <div class="card-body admin-menu-card-body d-flex flex-wrap">
      <a href="/admin/emails/welcome" class="admin-menu-item admin-menu-item-column">
        <p>Welcome Email</p>
        <span>Customize the email message sent when a user first joins</span>
      </a>
      <a href="/admin/emails/welcome" class="admin-menu-item admin-menu-item-column">
        <p>Onboarding Emails</p>
        <span>Email drip campaign sent on set schedule after user joins</span>
      </a>
      <a href="/admin/emails/notifications" class="admin-menu-item admin-menu-item-column">
        <p>Notifications</p>
        <span>Customize email and push notification messages</span>
      </a>
      <a href="/admin/system/onboarding" class="admin-menu-item">
        <p>Onboarding Settings</p>
        <span>Manage onboarding configurations </span>
      </a>
    </div>
  </div>
</div>
@endsection