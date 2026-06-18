<?php

namespace App;

enum DocumentRequestStatus : string
{
    case FOR_REVIEW = 'for_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FOR_COLLECTION = 'for_collection';
    case COLLECTED = 'collected';
    case FOR_DISPATCH = 'for_dispatch';
    case DISPATCHED = 'dispatched';
    case DELIVERED = 'delivered';
}
