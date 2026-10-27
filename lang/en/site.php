<?php

return [

    'skip_to_content' => 'Skip to content',

    'nav' => [
        'main' => 'Main navigation',
        'home' => 'Home',
        'disciplines' => 'Disciplines',
        'projects' => 'Projects',
        'news' => 'News',
        'about' => 'About us',
        'contact' => 'Contact',
        'menu' => 'Menu',
        'switch_language' => 'Read this page in :language',
    ],

    'cta' => [
        'request_proposal' => 'Request a proposal',
        'explore_projects' => 'Explore our projects',
        'view_all_projects' => 'View all projects',
        'view_all_news' => 'All news',
        'view_all_disciplines' => 'All disciplines',
        'learn_more' => 'Learn more',
        'read_article' => 'Read article',
        'view_project' => 'View project',
    ],

    'home' => [
        'stats' => [
            'years' => 'years of engineering practice',
            'projects' => 'projects in our portfolio',
            'disciplines' => 'engineering disciplines',
        ],
        'disciplines_title' => 'What we do',
        'disciplines_intro' => 'Multidisciplinary engineering under one roof, from concept design to site supervision.',
        'projects_title' => 'Featured projects',
        'projects_intro' => 'A selection of recent work across buildings, transport, utilities and industry.',
        'news_title' => 'Latest news',
        'cta_title' => 'Planning a project?',
        'cta_text' => 'Tell us about the scope, location and timeline. Our proposals team replies within two working days.',
    ],

    'disciplines' => [
        'title' => 'Disciplines',
        'intro' => 'Our engineers cover the full life cycle of a project, from feasibility and design to construction supervision.',
        'related_projects' => 'Selected projects',
        'other' => 'Other disciplines',
    ],

    'projects' => [
        'title' => 'Projects',
        'intro' => 'Buildings, infrastructure and industrial facilities we have engineered across the UAE and the wider region.',
        'sector_title' => ':sector projects',
        'filter_label' => 'Filter projects by sector',
        'all_sectors' => 'All sectors',
        'empty' => 'There are no projects in this sector yet.',
        'ongoing' => 'Ongoing',
        'related' => 'Related projects',
        'facts' => [
            'title' => 'Project facts',
            'sector' => 'Sector',
            'location' => 'Location',
            'client' => 'Client',
            'completion' => 'Completion',
            'disciplines' => 'Disciplines',
        ],
    ],

    'news' => [
        'title' => 'News',
        'intro' => 'Company updates, project milestones and insights from our engineering teams.',
        'category_title' => 'News: :category',
        'filter_label' => 'Filter news by topic',
        'all_categories' => 'All topics',
        'empty' => 'There is no news in this topic yet.',
        'more' => 'More news',
    ],

    'client_types' => [
        'government' => 'Government',
        'semi_government' => 'Semi-government',
        'private' => 'Private sector',
        'developer' => 'Real estate developer',
    ],

    'contact' => [
        'details' => 'Contact details',
        'address' => 'Address',
        'phone' => 'Phone',
        'email' => 'Email',
        'proposals_email' => 'Proposals',
        'hours' => 'Office hours',
        'map' => 'Open in maps',
    ],

    'rfp' => [
        'title' => 'Request for proposal',
        'intro' => 'Share the essentials of your project and our proposals team will contact you within two working days.',
        'optional' => 'optional',
        'select_sector' => 'Select a sector',
        'attachment_help' => 'Brief, drawings list or bill of quantities. PDF, Word (.docx) or Excel (.xlsx), up to 10 MB.',
        'privacy' => 'We use these details only to prepare a proposal. Attachments are stored privately and are visible to our proposals team only.',
        'submit' => 'Send request',
        'errors_title' => 'Please correct the highlighted fields.',
        'honeypot' => 'Leave this field empty',
        'fields' => [
            'name' => 'Full name',
            'company' => 'Company',
            'email' => 'Email address',
            'phone' => 'Phone number',
            'sector' => 'Sector',
            'project_location' => 'Project location',
            'message' => 'Project description',
            'attachment' => 'Attachment',
        ],
        // Field names as they appear inside validation messages.
        'attributes' => [
            'name' => 'full name',
            'company' => 'company',
            'email' => 'email address',
            'phone' => 'phone number',
            'sector' => 'sector',
            'project_location' => 'project location',
            'message' => 'project description',
            'attachment' => 'attachment',
        ],
        'placeholders' => [
            'phone' => '+971 50 000 0000',
            'project_location' => 'City or area',
            'message' => 'Scope, size, current design stage and expected timeline.',
        ],
        'validation' => [
            'phone' => 'Please enter a valid phone number, including the country code.',
        ],
        'thanks' => [
            'title' => 'Thank you',
            'heading' => 'Your request has been received',
            'text' => 'Thank you for considering Masar. A member of our proposals team will review your request and contact you within two working days.',
            'reference' => 'Your reference number is :reference. Please quote it in any correspondence.',
            'back_home' => 'Back to the home page',
        ],
    ],

    'languages' => [
        'en' => 'English',
        'ar' => 'Arabic',
    ],

    'fallback_notice' => 'This content is not yet available in :language and is shown in :fallback.',

    'footer' => [
        'explore' => 'Explore',
        'company' => 'Company',
        'follow' => 'Follow us',
        'rights' => 'All rights reserved.',
    ],

    'pagination' => [
        'label' => 'Pagination',
        'previous' => 'Previous',
        'next' => 'Next',
        'page' => 'Page :page',
    ],

    'social' => [
        'linkedin' => 'LinkedIn',
        'instagram' => 'Instagram',
        'x' => 'X',
        'youtube' => 'YouTube',
    ],

    'errors' => [
        'home' => 'Go to the home page',
        '403' => [
            'title' => 'Access denied',
            'text' => 'You do not have permission to open this page.',
        ],
        '404' => [
            'title' => 'Page not found',
            'text' => 'The page you are looking for may have moved or no longer exists.',
        ],
        '419' => [
            'title' => 'Page expired',
            'text' => 'Your session expired. Please go back, refresh the page and try again.',
        ],
        '429' => [
            'title' => 'Too many requests',
            'text' => 'You have sent several requests in a short time. Please wait a minute and try again.',
        ],
        '500' => [
            'title' => 'Something went wrong',
            'text' => 'An unexpected error occurred on our side. Please try again in a few minutes.',
        ],
        '503' => [
            'title' => 'Back shortly',
            'text' => 'The website is undergoing scheduled maintenance. Please check back soon.',
        ],
    ],

];
