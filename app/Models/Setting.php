<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * One site setting. Read settings through App\Support\Settings\SiteSettings,
 * which applies defaults and caching.
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    use LogsActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    public function activityLabel(): string
    {
        return $this->key;
    }
}
