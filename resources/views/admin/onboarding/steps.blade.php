@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
    ]])
    @endcomponent

    <h2>Onboarding Steps</h2>

    <table class="table">
        <thead>
            <tr>
                <td class="text-center" style="width: 3em;"><b>order</b></td>
                <td><b>step</b></td>
                <td class="text-center"><b>active</b></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php $step = 1; ?>
            <tr>
                <td class="text-center">
                    @if($settings['intro']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Welcome Message</b></p>
                    <p class="mb-0 text-muted">A short 2-3 sentence customizable message that greets new users and informs them of your platform and it's purpose.</p>
                </td>
                <td class="text-center">@if($settings['intro']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/welcome">Edit</a></td>
            </tr>
            <tr>
                <td class="text-center">
                    @if($settings['embed_video_active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Intro Video</b></p>
                    <p class="mb-0 text-muted">Optional video you can add. Instruct new users on your platforms purpose, teach them how to use functions important to you, or just say hi!</p>
                </td>
                <td class="text-center">@if($settings['embed_video_active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/intro-video">Edit</a></td>
            </tr>
            <tr>
                <td class="text-center">
                    @if($settings['basic']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Basic Details: Name/Location/etc</b></p>
                    <p class="mb-0 text-muted">Quick basic details step. Get their name, their location, their job title, and any pronouns (if enabled system-wide) they may use.</p>
                </td>
                <td class="text-center">@if($settings['basic']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/basic">Edit</a></td>
            </tr>
            <tr>
                <td class="text-center">
                    @if($settings['imagebio']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Image &amp; Bio</b></p>
                    <p class="mb-0 text-muted">Let's users upload their profile photo and input their (if enabled) bio.</p>
                </td>
                <td class="text-center">@if($settings['imagebio']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/image-bio">Edit</a></td>
            </tr>
            <tr>
                <td class="text-center">
                    @if($settings['about']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>About Me &amp; Mentor Status</b></p>
                    <p class="mb-0 text-muted">Users fill out a longer-form input "About Me" and indicate their mentor status.</p>
                </td>
                <td class="text-center">@if($settings['about']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/about-me">Edit</a></td>
            </tr>
            @foreach(App\Taxonomy::editable()->sortBy('profile_order_key') as $category)
            <tr>
                <td class="text-center">{{ $step }}<?php $step += 1; ?></td>
                <td><b>Category: {{ $category->name }}</b></td>
                <td class="text-center"><i class="fas fa-check"></i></td>
                <td class="text-right"><a href="/admin/system/onboarding/category/{{ $category->id }}">Edit</a></td>
            </tr>
            @endforeach
            <tr>
                <td class="text-center">
                    @if($settings['questions']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Profile Questions</b></p>
                    <p class="mb-0 text-muted">Users are prompted to answer any custom profile questions that are configured.</p>
                </td>
                <td class="text-center">@if($settings['questions']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/questions">Edit</a></td>
            </tr>
            <tr>
                <td class="text-center">
                    @if($settings['notifications']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Notifications</b></p>
                    <p class="mb-0 text-muted">Let's users choose their notification settings (including turning on push notifications for the device they are using at that time).</p>
                </td>
                <td class="text-center">@if($settings['notifications']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/notifications">Edit</a></td>
            </tr>
            <tr>
                <td class="text-center">
                    @if($settings['groups']['active'] == true)
                        {{ $step }}<?php $step += 1; ?>
                    @endif
                </td>
                <td>
                    <p class="mb-0"><b>Groups Selection</b></p>
                    <p class="mb-0 text-muted">Allows users to select which joinable groups they'd wish to add themselves to.</p>
                </td>
                <td class="text-center">@if($settings['groups']['active'] == true)<i class="fas fa-check"></i>@endif</td>
                <td class="text-right"><a href="/admin/system/onboarding/groups-selection">Edit</a></td>
            </tr>

            @if(getsetting('is_gdpr_enabled'))
            <tr>
                <td class="text-center">{{ $step }}<?php $step += 1; ?></td>
                <td>
                    <p class="mb-0"><b>GDPR</b></p>
                    <p class="mb-0 text-muted">If enabled on your platform, show an opt-in prompt required for GDPR compliance.</p>
                </td>
                <td class="text-center"><i class="fas fa-check"></i></td> <!-- Cannot be disabled if turned on via super admin instance settings -->
                <td class="text-right"><a href="/admin/system/onboarding/gdpr">Edit</a></td>
            </tr>
            @endif

            <tr>
                <td class="text-center">{{ $step }}<?php $step += 1; ?></td>
                <td>
                    <p class="mb-0"><b>Completed Message</b></p>
                    <p class="mb-0 text-muted">A short, "All done!" message with a button to go straight to their personalized dashboard.</p>
                </td>
                <td class="text-center"><i class="fas fa-check"></i></td> <!-- Cannot be disabled -->
                <td class="text-right"><a href="/admin/system/onboarding/completed">Edit</a></td>
            </tr>
        </tbody>
    </table>

@endsection