<?php

namespace SamuelMwangiW\Africastalking\Enum;

enum CallHangupCauses: string
{
    case NORMAL_CLEARING = 'NORMAL_CLEARING';
    case CALL_REJECTED = 'CALL_REJECTED';
    case NORMAL_TEMPORARY_FAILURE = 'NORMAL_TEMPORARY_FAILURE';
    case RECOVERY_ON_TIMER_EXPIRE = 'RECOVERY_ON_TIMER_EXPIRE';
    case ORIGINATOR_CANCEL = 'ORIGINATOR_CANCEL';
    case LOSE_RACE = 'LOSE_RACE';
    case USER_BUSY = 'USER_BUSY';
    case NO_ANSWER = 'NO_ANSWER';
    case NO_USER_RESPONSE = 'NO_USER_RESPONSE';
    case SUBSCRIBER_ABSENT = 'SUBSCRIBER_ABSENT';
    case SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
    case USER_NOT_REGISTERED = 'USER_NOT_REGISTERED';
    case UNALLOCATED_NUMBER = 'UNALLOCATED_NUMBER';
    case UNSPECIFIED = 'UNSPECIFIED';
}
