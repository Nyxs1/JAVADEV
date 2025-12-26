<?php

namespace App\Support;

class TechIconResolver
{
    /**
     * Simple Icons CDN base URL.
     */
    private const SIMPLE_ICONS_CDN = 'https://cdn.simpleicons.org';

    /**
     * Known tech name to Simple Icons slug mapping.
     * Keys are lowercase for case-insensitive matching.
     * Values are official Simple Icons slugs.
     */
    private static array $iconMap = [
        // Languages
        'json' => 'json',
        'javascript' => 'javascript',
        'js' => 'javascript',
        'typescript' => 'typescript',
        'ts' => 'typescript',
        'php' => 'php',
        'python' => 'python',
        'java' => 'openjdk',
        'go' => 'go',
        'golang' => 'go',
        'rust' => 'rust',
        'ruby' => 'ruby',
        'c#' => 'csharp',
        'csharp' => 'csharp',
        'c++' => 'cplusplus',
        'cpp' => 'cplusplus',
        'html' => 'html5',
        'css' => 'css3',
        'sql' => 'mysql',

        // Frameworks
        'laravel' => 'laravel',
        'vite' => 'vite',
        'react' => 'react',
        'reactjs' => 'react',
        'vue' => 'vuedotjs',
        'vuejs' => 'vuedotjs',
        'vue.js' => 'vuedotjs',
        'angular' => 'angular',
        'express' => 'express',
        'expressjs' => 'express',
        'nextjs' => 'nextdotjs',
        'next.js' => 'nextdotjs',
        'nuxt' => 'nuxtdotjs',
        'nuxtjs' => 'nuxtdotjs',
        'django' => 'django',
        'flask' => 'flask',
        'spring' => 'spring',
        'rails' => 'rubyonrails',
        'ruby on rails' => 'rubyonrails',
        'tailwind' => 'tailwindcss',
        'tailwindcss' => 'tailwindcss',
        'bootstrap' => 'bootstrap',

        // Tools
        'postman' => 'postman',
        'swagger' => 'swagger',
        'openapi' => 'openapiinitiative',
        'git' => 'git',
        'github' => 'github',
        'gitlab' => 'gitlab',
        'docker' => 'docker',
        'kubernetes' => 'kubernetes',
        'k8s' => 'kubernetes',
        'vs code' => 'visualstudiocode',
        'vscode' => 'visualstudiocode',
        'visual studio code' => 'visualstudiocode',
        'terminal' => 'gnometerminal',
        'npm' => 'npm',
        'yarn' => 'yarn',
        'composer' => 'composer',
        'webpack' => 'webpack',
        'figma' => 'figma',
        'insomnia' => 'insomnia',

        // Databases
        'mysql' => 'mysql',
        'postgresql' => 'postgresql',
        'postgres' => 'postgresql',
        'mongodb' => 'mongodb',
        'mongo' => 'mongodb',
        'redis' => 'redis',
        'sqlite' => 'sqlite',
        'mariadb' => 'mariadb',
        'firebase' => 'firebase',

        // API/Protocols
        'graphql' => 'graphql',

        // Cloud/Services
        'aws' => 'amazonaws',
        'azure' => 'microsoftazure',
        'gcp' => 'googlecloud',
        'google cloud' => 'googlecloud',
        'heroku' => 'heroku',
        'vercel' => 'vercel',
        'netlify' => 'netlify',

        // Runtime
        'nodejs' => 'nodedotjs',
        'node.js' => 'nodedotjs',
        'node' => 'nodedotjs',
        'deno' => 'deno',
        'bun' => 'bun',
    ];

    /**
     * Category-based fallback icons.
     */
    private static array $categoryFallbacks = [
        'language' => 'language',
        'framework' => 'framework',
        'tools' => 'tools',
        'database' => 'database',
        'other' => 'generic',
    ];

    /**
     * Resolve the icon path for a tech item.
     *
     * @param string $title The tech item title
     * @param string|null $explicitIcon Explicit icon slug from database
     * @param string|null $category The tech category
     * @return string The resolved icon filename (without path)
     */
    public static function resolve(string $title, ?string $explicitIcon = null, ?string $category = null): string
    {
        $basePath = public_path('icons/tech');

        // 1. Explicit icon field
        if ($explicitIcon) {
            $path = "{$basePath}/{$explicitIcon}.svg";
            if (file_exists($path)) {
                return $explicitIcon;
            }
        }

        // 2. Auto-map by tech name (case-insensitive)
        $normalizedTitle = strtolower(trim($title));
        if (isset(self::$iconMap[$normalizedTitle])) {
            $mapped = self::$iconMap[$normalizedTitle];
            $path = "{$basePath}/{$mapped}.svg";
            if (file_exists($path)) {
                return $mapped;
            }
        }

        // 3. Try slugified title
        $slug = \Illuminate\Support\Str::slug($title);
        $path = "{$basePath}/{$slug}.svg";
        if (file_exists($path)) {
            return $slug;
        }

        // 4. Category-based fallback
        if ($category && isset(self::$categoryFallbacks[$category])) {
            $fallback = self::$categoryFallbacks[$category];
            $path = "{$basePath}/{$fallback}.svg";
            if (file_exists($path)) {
                return $fallback;
            }
        }

        // 5. Final fallback
        return 'generic';
    }

    /**
     * Get the full URL for a tech icon using Simple Icons CDN.
     * Uses official brand colors (no color override).
     * Falls back to local icons for category/generic fallbacks.
     */
    public static function url(string $title, ?string $explicitIcon = null, ?string $category = null): string
    {
        // 1. Check explicit icon in local storage first
        if ($explicitIcon) {
            $localPath = public_path("icons/tech/{$explicitIcon}.svg");
            if (file_exists($localPath)) {
                return asset("icons/tech/{$explicitIcon}.svg");
            }
        }

        // 2. Try to resolve from Simple Icons mapping (official brand colors)
        $normalizedTitle = strtolower(trim($title));
        if (isset(self::$iconMap[$normalizedTitle])) {
            $simpleIconSlug = self::$iconMap[$normalizedTitle];
            // No color param = official brand color
            return self::SIMPLE_ICONS_CDN . "/{$simpleIconSlug}";
        }

        // 3. Try slugified title against Simple Icons
        $slug = \Illuminate\Support\Str::slug($title);
        if (isset(self::$iconMap[$slug])) {
            return self::SIMPLE_ICONS_CDN . "/" . self::$iconMap[$slug];
        }

        // 4. Category-based fallback (local icons)
        if ($category && isset(self::$categoryFallbacks[$category])) {
            $fallback = self::$categoryFallbacks[$category];
            $localPath = public_path("icons/tech/{$fallback}.svg");
            if (file_exists($localPath)) {
                return asset("icons/tech/{$fallback}.svg");
            }
        }

        // 5. Final fallback (local generic icon)
        return asset("icons/tech/generic.svg");
    }

    /**
     * Get Simple Icons CDN URL directly for a known slug.
     * Omit color param to use official brand color.
     */
    public static function simpleIconUrl(string $slug, ?string $color = null): string
    {
        if ($color) {
            return self::SIMPLE_ICONS_CDN . "/{$slug}/{$color}";
        }
        return self::SIMPLE_ICONS_CDN . "/{$slug}";
    }

    /**
     * Check if a tech name has a known Simple Icons mapping.
     */
    public static function hasSimpleIcon(string $title): bool
    {
        $normalizedTitle = strtolower(trim($title));
        return isset(self::$iconMap[$normalizedTitle]);
    }
}
