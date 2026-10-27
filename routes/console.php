<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune', ['--model' => [ActivityLog::class]])->daily();
