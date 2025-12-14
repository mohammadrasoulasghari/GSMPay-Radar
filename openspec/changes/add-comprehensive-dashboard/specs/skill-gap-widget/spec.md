# Spec: Skill & Gap Chart Widget

## ADDED Requirements

#### Requirement: Skill Gap Chart Display
The Skill & Gap widget displays a bar chart of recurring mistake categories.

**Scenarios:**
1. Widget displays horizontal or vertical bar chart
2. X-axis: Mistake categories (Testing, Naming, Documentation, Architecture, Performance, Security, Other)
3. Y-axis: Count of mistakes in each category from past 30 days
4. Each bar is color-coded (different color per category)
5. Chart is responsive and readable on mobile
6. Chart title: "تحلیل مهارت و نیاز" (Skill & Gap Analysis)
7. All text is in Persian

**Implementation Notes:**
- Extends `Filament\Widgets\ChartWidget`
- Chart type: Bar (horizontal or vertical, based on space)
- Implement `getChartType(): ChartType` returning `ChartType::Bar`
- Implement `getChartData(): array` with datasets and labels

---

#### Requirement: Mistake Aggregation & Categorization
The widget must parse `raw_analysis` JSON and categorize recurring mistakes.

**Scenarios:**
1. Query: All PrReports from past 30 days
2. For each PR, extract `raw_analysis['recurring_mistakes']` array
3. Each mistake string is categorized based on keyword matching
4. Categories: Testing, Naming, Documentation, Architecture, Performance, Security, Other
5. Count mistakes per category
6. Return aggregated counts sorted by frequency (descending)

**Implementation Notes:**
- Method: `parseRecurringMistakes(array $rawAnalysis): array`
  ```php
  protected function parseRecurringMistakes(array $rawAnalysis): array
  {
      return $rawAnalysis['recurring_mistakes'] ?? [];
  }
  ```
- Method: `categorizeError(string $mistake): string`
  ```php
  protected function categorizeError(string $mistake): string
  {
      $mistake = strtolower($mistake);
      
      $categories = [
          'Testing' => ['test', 'unit', 'integration', 'coverage', 'mock', 'spec'],
          'Naming' => ['name', 'clarity', 'variable', 'function', 'readable', 'semantic'],
          'Documentation' => ['comment', 'doc', 'readme', 'explanation', 'describe', 'javadoc'],
          'Architecture' => ['design', 'pattern', 'structure', 'responsibility', 'separation', 'layer'],
          'Performance' => ['slow', 'optimize', 'efficiency', 'memory', 'query', 'loop', 'cache'],
          'Security' => ['vulnerability', 'injection', 'auth', 'validation', 'encrypt', 'sanitize'],
      ];
      
      foreach ($categories as $category => $keywords) {
          foreach ($keywords as $keyword) {
              if (strpos($mistake, $keyword) !== false) {
                  return $category;
              }
          }
      }
      
      return 'Other';
  }
  ```

---

#### Requirement: Chart Data Aggregation
The widget caches aggregated mistake data for performance.

**Scenarios:**
1. Query all PRs from past 30 days
2. For each PR, extract and categorize mistakes
3. Count total per category
4. Filter out empty categories
5. Sort by count (descending)
6. Cache result under key `dashboard:skill_gap`, duration 2 hours

**Implementation Notes:**
- Method: `getMistakesAggregation(): array`
  ```php
  protected function getMistakesAggregation(): array
  {
      return Cache::remember('dashboard:skill_gap', now()->addHours(2), function () {
          $reports = PrReport::where('created_at', '>=', now()->subMonth())->get();
          $categoryCounts = array_fill_keys(
              ['Testing', 'Naming', 'Documentation', 'Architecture', 'Performance', 'Security', 'Other'],
              0
          );
          
          $reports->each(function ($pr) use (&$categoryCounts) {
              $mistakes = $this->parseRecurringMistakes($pr->raw_analysis ?? []);
              foreach ($mistakes as $mistake) {
                  $category = $this->categorizeError($mistake);
                  $categoryCounts[$category]++;
              }
          });
          
          return collect($categoryCounts)
              ->filter(fn($count) => $count > 0)
              ->sortDesc()
              ->toArray();
      });
  }
  ```

---

#### Requirement: Chart Data Format
The widget returns Filament-compatible chart data structure.

**Scenarios:**
1. Implement `getChartData(): array`
2. Return structure:
   ```php
   return [
       'datasets' => [
           [
               'label' => 'Mistake Count',
               'data' => [5, 8, 3, 12, 2, 1, 4],
               'backgroundColor' => [
                   '#3b82f6', // Testing - blue
                   '#10b981', // Naming - green
                   '#f59e0b', // Documentation - amber
                   '#8b5cf6', // Architecture - purple
                   '#ec4899', // Performance - pink
                   '#ef4444', // Security - red
                   '#6b7280', // Other - gray
               ],
           ],
       ],
       'labels' => ['Testing', 'Naming', 'Documentation', 'Architecture', 'Performance', 'Security', 'Other'],
   ];
   ```
3. Colors should match Sky Blue theme where applicable

**Implementation Notes:**
- Chart colors should be visually distinct and accessibility-friendly
- Data order matches labels order
- Can use Filament's color helpers if available

---

#### Requirement: Error Handling & Robustness
The widget must handle malformed JSON and missing fields gracefully.

**Scenarios:**
1. If `raw_analysis` is null → treated as empty array
2. If `recurring_mistakes` is null → treated as empty array
3. If JSON parsing fails → log warning, skip PR, continue
4. Widget displays "No data available" if all PRs fail or none exist
5. No exceptions thrown to user

**Implementation Notes:**
- Wrap parsing in try-catch, log errors to Laravel log
- Use null coalescing: `$analysis['key'] ?? []`
- Test with various malformed JSON structures

---

#### Requirement: Skill Gap Translations
All Skill Gap labels must be in Persian.

**Scenarios:**
1. Widget title: `__('dashboard.skill_gap')`
2. Chart legend label: `__('dashboard.mistake_count')`
3. Category labels: 
   - Testing: `__('dashboard.category_testing')`
   - Naming: `__('dashboard.category_naming')`
   - Documentation: `__('dashboard.category_documentation')`
   - Architecture: `__('dashboard.category_architecture')`
   - Performance: `__('dashboard.category_performance')`
   - Security: `__('dashboard.category_security')`
   - Other: `__('dashboard.category_other')`
4. Empty state: `__('dashboard.skill_gap_empty')` → "No skill gap data available for the selected period"

**Implementation Notes:**
- Add all keys to `lang/fa/dashboard.php`
- Use `__()` in widget for text, but keep category names in code as array keys (for consistency)
