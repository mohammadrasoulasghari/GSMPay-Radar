<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-trophy class="w-5 h-5 text-primary-500" />
                <span>{{ $this->getHeading() }}</span>
            </div>
        </x-slot>

        @php
            $leaderboard = $this->getLeaderboardData();
        @endphp

        @if (empty($leaderboard))
            <div class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-user-group class="w-12 h-12 mb-2 opacity-50" />
                <p class="text-sm">{{ __('dashboard.no_reviewers_data') }}</p>
            </div>
        @else
            <div class="overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-3 py-2 text-right font-medium">{{ __('dashboard.rank') }}</th>
                            <th class="px-3 py-2 text-right font-medium">{{ __('dashboard.reviewer') }}</th>
                            <th class="px-3 py-2 text-center font-medium">{{ __('dashboard.avg_tone_score') }}</th>
                            <th class="px-3 py-2 text-center font-medium">{{ __('dashboard.review_count') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($leaderboard as $index => $reviewer)
                            @php
                                $rank = $index + 1;
                            @endphp
                            <tr class="{{ $this->getRowBackground($rank) }} hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-3 py-3 text-right">
                                    <span class="text-lg">{{ $this->getMedal($rank) }}</span>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $reviewer['name'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $this->getToneColor($reviewer['avg_tone']) }}">
                                        {{ number_format($reviewer['avg_tone'], 1) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center text-gray-500 dark:text-gray-400">
                                    {{ $reviewer['review_count'] }} {{ __('dashboard.reviews') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
