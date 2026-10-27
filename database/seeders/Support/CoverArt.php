<?php

namespace Database\Seeders\Support;

use GdImage;
use RuntimeException;

/**
 * Draws simple architectural illustrations used as cover images for the
 * seeded projects and news articles, so a fresh installation looks complete
 * without shipping photographs in the repository.
 */
final class CoverArt
{
    private const WIDTH = 1200;

    private const HEIGHT = 900;

    /** Drawn at twice the size and scaled down for smooth edges. */
    private const SCALE = 2;

    private const PALETTES = [
        'dusk' => ['#123744', '#2a8299', '#0a222c', '#cf9c5c'],
        'sand' => ['#c3843f', '#f4eadb', '#5b3a22', '#123744'],
        'sea' => ['#164556', '#7dbfcf', '#0a222c', '#f4eadb'],
        'night' => ['#0a222c', '#195668', '#04131a', '#dbb886'],
    ];

    private GdImage $image;

    /**
     * @return string JPEG binary data.
     */
    public static function render(string $scene, string $palette, int $seed): string
    {
        return (new self)->draw($scene, self::PALETTES[$palette] ?? self::PALETTES['dusk'], $seed);
    }

    /**
     * @param  array{0: string, 1: string, 2: string, 3: string}  $colors  Sky top, sky bottom, structure, accent.
     */
    private function draw(string $scene, array $colors, int $seed): string
    {
        mt_srand($seed);

        $width = self::WIDTH * self::SCALE;
        $height = self::HEIGHT * self::SCALE;
        $this->image = imagecreatetruecolor($width, $height);

        $this->gradient($colors[0], $colors[1]);
        $this->grid();
        $this->sun($colors[3]);

        match ($scene) {
            'towers' => $this->towers($colors[2], $colors[3]),
            'bridge' => $this->bridge($colors[2], $colors[3]),
            'flyover' => $this->flyover($colors[2], $colors[3]),
            'campus' => $this->campus($colors[2], $colors[3]),
            'industrial' => $this->industrial($colors[2], $colors[3]),
            'solar' => $this->solar($colors[2], $colors[3]),
            'pipes' => $this->pipes($colors[2], $colors[3]),
            default => throw new RuntimeException("Unknown cover scene [{$scene}]."),
        };

        $output = imagescale($this->image, self::WIDTH, self::HEIGHT, IMG_TRIANGLE)
            ?: throw new RuntimeException('The cover image could not be scaled.');

        ob_start();
        imagejpeg($output, null, 82);

        return (string) ob_get_clean();
    }

    private function gradient(string $top, string $bottom): void
    {
        [$r1, $g1, $b1] = $this->rgb($top);
        [$r2, $g2, $b2] = $this->rgb($bottom);
        $height = imagesy($this->image);

        for ($y = 0; $y < $height; $y++) {
            $t = $y / $height;
            $color = imagecolorallocate($this->image, (int) ($r1 + ($r2 - $r1) * $t), (int) ($g1 + ($g2 - $g1) * $t), (int) ($b1 + ($b2 - $b1) * $t));
            imageline($this->image, 0, $y, imagesx($this->image), $y, $color);
        }
    }

    private function grid(): void
    {
        $line = imagecolorallocatealpha($this->image, 255, 255, 255, 115);
        $step = 60 * self::SCALE;

        for ($x = 0; $x < imagesx($this->image); $x += $step) {
            imagefilledrectangle($this->image, $x, 0, $x + self::SCALE - 1, imagesy($this->image), $line);
        }

        for ($y = 0; $y < imagesy($this->image); $y += $step) {
            imagefilledrectangle($this->image, 0, $y, imagesx($this->image), $y + self::SCALE - 1, $line);
        }
    }

    private function sun(string $accent): void
    {
        $color = $this->color($accent, 40);
        imagefilledellipse($this->image, $this->s(mt_rand(780, 1000)), $this->s(mt_rand(150, 260)), $this->s(170), $this->s(170), $color);
    }

    private function ground(string $color, int $top = 760): void
    {
        imagefilledrectangle($this->image, 0, $this->s($top), imagesx($this->image), imagesy($this->image), $this->color($color));
    }

    private function towers(string $structure, string $accent): void
    {
        $back = $this->color($structure, 60);
        for ($x = -40; $x < 1240; $x += mt_rand(70, 120)) {
            $top = mt_rand(380, 620);
            imagefilledrectangle($this->image, $this->s($x), $this->s($top), $this->s($x + mt_rand(50, 90)), $this->s(780), $back);
        }

        $front = $this->color($structure);
        $window = $this->color($accent, 70);
        foreach ([[300, 210, 150], [500, 120, 170], [720, 300, 140]] as [$x, $top, $width]) {
            imagefilledrectangle($this->image, $this->s($x), $this->s($top), $this->s($x + $width), $this->s(780), $front);

            for ($y = $top + 30; $y < 760; $y += 34) {
                for ($wx = $x + 18; $wx < $x + $width - 18; $wx += 28) {
                    if (mt_rand(0, 3) > 0) {
                        imagefilledrectangle($this->image, $this->s($wx), $this->s($y), $this->s($wx + 12), $this->s($y + 16), $window);
                    }
                }
            }
        }

        $this->ground($structure);
    }

    private function bridge(string $structure, string $accent): void
    {
        $water = $this->color('#ffffff', 105);
        imagefilledrectangle($this->image, 0, $this->s(700), imagesx($this->image), imagesy($this->image), $water);

        $steel = $this->color($structure);
        imagesetthickness($this->image, $this->s(14));
        imagearc($this->image, $this->s(600), $this->s(680), $this->s(860), $this->s(620), 180, 360, $steel);

        imagesetthickness($this->image, $this->s(3));
        for ($x = 220; $x <= 980; $x += 40) {
            $dx = ($x - 600) / 430;
            $archY = 680 - (int) (310 * sqrt(max(0, 1 - $dx * $dx)));
            imageline($this->image, $this->s($x), $this->s($archY), $this->s($x), $this->s(660), $steel);
        }

        imagesetthickness($this->image, 1);
        imagefilledrectangle($this->image, 0, $this->s(655), imagesx($this->image), $this->s(680), $steel);
        imagefilledrectangle($this->image, 0, $this->s(648), imagesx($this->image), $this->s(655), $this->color($accent));
    }

    private function flyover(string $structure, string $accent): void
    {
        $this->ground($structure, 780);
        $deck = $this->color($structure);
        $shade = $this->color($structure, 50);

        foreach ([140, 380, 620, 860, 1100] as $x) {
            imagefilledrectangle($this->image, $this->s($x - 18), $this->s(560), $this->s($x + 18), $this->s(780), $shade);
        }

        imagefilledrectangle($this->image, 0, $this->s(520), imagesx($this->image), $this->s(565), $deck);
        imagefilledrectangle($this->image, 0, $this->s(512), imagesx($this->image), $this->s(520), $this->color($accent));

        $marking = $this->color('#ffffff', 40);
        for ($x = 0; $x < 1200; $x += 90) {
            imagefilledrectangle($this->image, $this->s($x), $this->s(838), $this->s($x + 50), $this->s(846), $marking);
        }
    }

    private function campus(string $structure, string $accent): void
    {
        $front = $this->color($structure);
        $roof = $this->color($accent);
        foreach ([[80, 560, 330], [440, 500, 360], [830, 580, 300]] as [$x, $top, $width]) {
            imagefilledrectangle($this->image, $this->s($x), $this->s($top), $this->s($x + $width), $this->s(780), $front);
            imagefilledrectangle($this->image, $this->s($x - 15), $this->s($top - 14), $this->s($x + $width + 15), $this->s($top), $roof);

            for ($wx = $x + 25; $wx < $x + $width - 40; $wx += 55) {
                imagefilledrectangle($this->image, $this->s($wx), $this->s($top + 45), $this->s($wx + 30), $this->s(740), $this->color('#ffffff', 95));
            }
        }

        $tree = $this->color('#7dbfcf', 45);
        foreach ([60, 420, 800, 1150] as $x) {
            imagefilledellipse($this->image, $this->s($x), $this->s(720), $this->s(90), $this->s(110), $tree);
        }

        $this->ground($structure);
    }

    private function industrial(string $structure, string $accent): void
    {
        $front = $this->color($structure);
        foreach ([[60, 520], [420, 470], [780, 540]] as [$x, $top]) {
            imagefilledpolygon($this->image, [
                $this->s($x), $this->s($top + 60),
                $this->s($x + 170), $this->s($top),
                $this->s($x + 340), $this->s($top + 60),
                $this->s($x + 340), $this->s(780),
                $this->s($x), $this->s(780),
            ], $front);
            imagefilledrectangle($this->image, $this->s($x + 120), $this->s(680), $this->s($x + 220), $this->s(780), $this->color($accent, 30));
        }

        $tank = $this->color($structure, 40);
        imagefilledrectangle($this->image, $this->s(1080), $this->s(560), $this->s(1180), $this->s(780), $tank);
        imagefilledellipse($this->image, $this->s(1130), $this->s(560), $this->s(100), $this->s(36), $tank);

        $this->ground($structure);
    }

    private function solar(string $structure, string $accent): void
    {
        $this->ground($structure, 800);
        $panel = $this->color($structure);
        $frame = $this->color($accent);

        foreach ([[40, 560], [420, 600], [800, 560]] as [$x, $top]) {
            imagefilledpolygon($this->image, [
                $this->s($x), $this->s($top + 70),
                $this->s($x + 320), $this->s($top),
                $this->s($x + 340), $this->s($top + 22),
                $this->s($x + 20), $this->s($top + 92),
            ], $panel);
            imagefilledrectangle($this->image, $this->s($x + 40), $this->s($top + 80), $this->s($x + 52), $this->s(800), $frame);
            imagefilledrectangle($this->image, $this->s($x + 280), $this->s($top + 20), $this->s($x + 292), $this->s(800), $frame);
        }
    }

    private function pipes(string $structure, string $accent): void
    {
        $soil = $this->color($structure, 20);
        imagefilledrectangle($this->image, 0, $this->s(520), imagesx($this->image), imagesy($this->image), $soil);

        foreach ([[250, 700, 150], [600, 690, 190], [950, 710, 130]] as [$x, $y, $diameter]) {
            imagefilledellipse($this->image, $this->s($x), $this->s($y), $this->s($diameter), $this->s($diameter), $this->color($accent));
            imagefilledellipse($this->image, $this->s($x), $this->s($y), $this->s($diameter - 34), $this->s($diameter - 34), $this->color($structure));
        }

        imagefilledrectangle($this->image, 0, $this->s(505), imagesx($this->image), $this->s(520), $this->color($accent));
    }

    private function s(int|float $value): int
    {
        return (int) round($value * self::SCALE);
    }

    private function color(string $hex, int $alpha = 0): int
    {
        [$r, $g, $b] = $this->rgb($hex);

        return imagecolorallocatealpha($this->image, $r, $g, $b, $alpha);
    }

    /**
     * @return array{int, int, int}
     */
    private function rgb(string $hex): array
    {
        return array_map('hexdec', str_split(ltrim($hex, '#'), 2));
    }
}
