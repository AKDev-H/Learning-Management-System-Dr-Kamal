<?php

namespace App;

enum LessonType: string
{
    case Video = 'video';
    case Text = 'text';
    case Assignment = 'assignment';
    case Quiz = 'quiz';
    case Live = 'live';
}
