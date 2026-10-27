{{-- Inline styles: this view is rendered inside the Filament panel, whose prebuilt stylesheet does not include arbitrary utilities. --}}
@php($rows = \App\Support\Activity\ActivityChanges::rows($getRecord()))

@if ($rows === [])
    <p style="font-size: .875rem; color: var(--gray-500);">No field changes recorded.</p>
@else
    <div style="overflow-x: auto;">
        <table style="width: 100%; font-size: .875rem; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--gray-200);">
                    <th style="padding: .5rem 1rem .5rem 0; text-align: start; font-weight: 500; color: var(--gray-500);">Field</th>
                    <th style="padding: .5rem 1rem .5rem 0; text-align: start; font-weight: 500; color: var(--gray-500);">Before</th>
                    <th style="padding: .5rem 0; text-align: start; font-weight: 500; color: var(--gray-500);">After</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr style="border-bottom: 1px solid var(--gray-100); vertical-align: top;">
                        <td style="padding: .5rem 1rem .5rem 0; font-weight: 500;">{{ $row['attribute'] }}</td>
                        <td style="padding: .5rem 1rem .5rem 0; white-space: pre-line; color: var(--gray-500);" dir="auto">{{ $row['old'] ?? '—' }}</td>
                        <td style="padding: .5rem 0; white-space: pre-line;" dir="auto">{{ $row['new'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
