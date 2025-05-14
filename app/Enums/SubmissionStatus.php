<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    const SUBMITTED =  'submitted';
    const UNDER_REVIEW = 'under_review';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';
    const PUBLISHED = 'published';
    const CANCELLED =  'cancelled';
}
