<?php

namespace Database\Factories;

use App\Enums\ProposalRequestStatus;
use App\Models\ProposalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProposalRequest>
 */
class ProposalRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company' => fake()->company(),
            'email' => fake()->unique()->userName().'@clients.masar.test',
            'phone' => '+971 50 '.fake()->numerify('### ####'),
            'project_location' => fake()->randomElement(['Abu Dhabi', 'Dubai', 'Al Ain', 'Sharjah']),
            'message' => fake()->paragraph(4),
            'locale' => 'en',
        ];
    }

    public function withStatus(ProposalRequestStatus $status): static
    {
        return $this->afterCreating(function (ProposalRequest $proposalRequest) use ($status): void {
            $proposalRequest->forceFill(['status' => $status])->saveQuietly();
        });
    }

    /**
     * An attachment record pointing at a file the test is expected to create.
     */
    public function withAttachment(string $path = 'proposal-requests/2026/10/brief.pdf'): static
    {
        return $this->state([
            'attachment_disk' => 'private',
            'attachment_path' => $path,
            'attachment_original_name' => 'Project brief.pdf',
            'attachment_mime' => 'application/pdf',
            'attachment_size' => 2048,
        ]);
    }
}
