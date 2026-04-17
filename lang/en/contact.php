<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contact & Pages Language Lines (English)
    |--------------------------------------------------------------------------
    |
    | English strings for the contact form, footer pages, and UI text.
    |
    */

    // ─── Page Titles ──────────────────────────────────────────────────────────
    'page_title_contact'  => 'Contact Us',
    'page_title_about'    => 'About Us',
    'page_title_privacy'  => 'Privacy Policy',
    'page_title_terms'    => 'Terms of Use',

    // ─── Contact Form UI ──────────────────────────────────────────────────────
    'form_heading'             => 'Send a Message',
    'form_subheading'          => 'Fields marked with (*) are required.',
    'field_name'               => 'Your Name',
    'field_email'              => 'Email',
    'field_phone'              => 'Phone Number',
    'field_phone_optional'     => '(Optional)',
    'field_subject'            => 'Subject',
    'field_message'            => 'Message',
    'btn_send'                 => 'Send Message',
    'btn_sending'              => 'Sending...',
    'placeholder_name'         => 'Full name',
    'placeholder_phone'        => 'e.g. 01700000000',
    'placeholder_subject'      => '— Select a subject —',
    'placeholder_message'      => 'Describe your issue or message in detail... (minimum 20 characters)',
    'char_counter'             => ':used / :max',
    'privacy_consent'          => 'By sending a message you agree to our :link.',
    'privacy_consent_link'     => 'Privacy Policy',

    // ─── Success / Error Messages ─────────────────────────────────────────────
    'success'              => 'Your message has been sent successfully! We will get back to you soon.',
    'error_summary'        => 'There are some errors in the form:',

    // ─── Validation Errors ────────────────────────────────────────────────────
    'validation' => [
        // Name
        'name_required'    => 'Your name is required.',
        'name_min'         => 'Name must be at least 2 characters.',
        'name_max'         => 'Name may not exceed 120 characters.',
        // Email
        'email_required'   => 'An email address is required.',
        'email_email'      => 'Please provide a valid email address (e.g. name@example.com).',
        'email_max'        => 'The email address is too long.',
        // Phone
        'phone_max'        => 'Phone number may not exceed 20 digits.',
        // Subject
        'subject_required' => 'A subject is required.',
        'subject_min'      => 'Subject must be at least 5 characters.',
        'subject_max'      => 'Subject may not exceed 120 characters.',
        // Message
        'message_required' => 'The message body is required.',
        'message_min'      => 'Message must be at least 20 characters.',
        'message_max'      => 'Message may not exceed 2000 characters.',
        // Throttle
        'throttle'         => 'Too many messages sent. Please wait :seconds seconds before trying again.',
        'throttle_minutes' => 'Too many attempts. Please wait :minutes minutes before trying again.',
        // Honeypot
        'honeypot'         => 'Invalid request.',
    ],

    // ─── Admin — contact_messages ─────────────────────────────────────────────
    'admin' => [
        'section_title'      => 'Contact Messages',
        'status_new'         => 'New',
        'status_in_progress' => 'In Progress',
        'status_resolved'    => 'Resolved',
        'status_spam'        => 'Spam',
        'col_sender'         => 'Sender',
        'col_subject'        => 'Subject',
        'col_status'         => 'Status',
        'col_date'           => 'Date',
        'mark_resolved'      => 'Mark as Resolved',
        'mark_spam'          => 'Mark as Spam',
        'mark_in_progress'   => 'Mark In Progress',
        'no_messages'        => 'No messages found.',
        'notification_title' => 'New Contact Message',
        'notification_body'  => ':name sent you a message: ":subject"',
    ],

    // ─── About Page ───────────────────────────────────────────────────────────
    'about' => [
        'hero_badge'    => 'Our Purpose & Identity',
        'hero_title'    => 'About Us',
        'hero_subtitle' => 'Our mission is to make blood donation easy, safe, and dignified.',
        'mission_title' => 'Our Mission',
        'vision_title'  => 'Our Vision',
        'team_title'    => 'Our Team',
    ],

    // ─── Privacy Policy Page ──────────────────────────────────────────────────
    'privacy' => [
        'hero_badge'    => 'Legal Document',
        'hero_title'    => 'Privacy Policy',
        'hero_subtitle' => 'How we collect, store, and use your personal information.',
        'toc_title'     => 'Table of Contents',
        'last_updated'  => 'Last Updated:',
    ],

    // ─── Terms of Use Page ────────────────────────────────────────────────────
    'terms' => [
        'hero_badge'    => 'Legal Document',
        'hero_title'    => 'Terms of Use',
        'hero_subtitle' => 'Rules and conditions for using the RoktoDut platform.',
        'toc_title'     => 'Table of Contents',
        'last_updated'  => 'Last Updated:',
        'acceptance'    => 'By using RoktoDut you agree to these Terms of Use.',
    ],

];
