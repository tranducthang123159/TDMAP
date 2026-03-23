<?php

namespace App\Services;

class GeoJsonOptimizeService
{
    public function makeLite(array $geojson, int $step = 4): array
    {
        return $this->optimize($geojson, $step);
    }

    public function makeUltraLite(array $geojson, int $step = 10): array
    {
        return $this->optimize($geojson, $step);
    }

    protected function optimize(array $geojson, int $step): array
    {
        $features = $geojson['features'] ?? [];
        $out = [];

        foreach ($features as $feature) {
            if (
                !isset($feature['geometry']) ||
                !isset($feature['geometry']['type']) ||
                !isset($feature['geometry']['coordinates'])
            ) {
                continue;
            }

            $type = $feature['geometry']['type'];
            $coords = $feature['geometry']['coordinates'];

            $feature['geometry']['coordinates'] = $this->simplifyGeometry($type, $coords, $step);
            $out[] = $feature;
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $out,
        ];
    }

    protected function simplifyGeometry(string $type, $coordinates, int $step)
    {
        return match ($type) {
            'Polygon' => $this->simplifyPolygon($coordinates, $step),
            'MultiPolygon' => $this->simplifyMultiPolygon($coordinates, $step),
            'LineString' => $this->reducePoints($coordinates, $step, false),
            'MultiLineString' => $this->simplifyMultiLineString($coordinates, $step),
            default => $coordinates,
        };
    }

    protected function simplifyPolygon(array $polygon, int $step): array
    {
        $result = [];

        foreach ($polygon as $ring) {
            $result[] = $this->reducePoints($ring, $step, true);
        }

        return $result;
    }

    protected function simplifyMultiPolygon(array $multiPolygon, int $step): array
    {
        $result = [];

        foreach ($multiPolygon as $polygon) {
            $result[] = $this->simplifyPolygon($polygon, $step);
        }

        return $result;
    }

    protected function simplifyMultiLineString(array $multiLine, int $step): array
    {
        $result = [];

        foreach ($multiLine as $line) {
            $result[] = $this->reducePoints($line, $step, false);
        }

        return $result;
    }

    protected function reducePoints(array $points, int $step, bool $closed = false): array
    {
        $count = count($points);

        if ($count <= 12) {
            return $points;
        }

        $reduced = [];

        foreach ($points as $i => $point) {
            if ($i === 0 || $i === $count - 1 || $i % $step === 0) {
                $reduced[] = $point;
            }
        }

        if ($closed && count($reduced) >= 3) {
            $first = $reduced[0];
            $last = $reduced[count($reduced) - 1];

            if ($first !== $last) {
                $reduced[] = $first;
            }
        }

        return $reduced;
    }

    public function getBBox(array $geojson): ?array
    {
        $minLng = null;
        $minLat = null;
        $maxLng = null;
        $maxLat = null;

        $walk = function ($item) use (&$walk, &$minLng, &$minLat, &$maxLng, &$maxLat) {
            if (!is_array($item)) {
                return;
            }

            if (isset($item[0], $item[1]) && is_numeric($item[0]) && is_numeric($item[1])) {
                $lng = $item[0];
                $lat = $item[1];

                $minLng = is_null($minLng) ? $lng : min($minLng, $lng);
                $minLat = is_null($minLat) ? $lat : min($minLat, $lat);
                $maxLng = is_null($maxLng) ? $lng : max($maxLng, $lng);
                $maxLat = is_null($maxLat) ? $lat : max($maxLat, $lat);
                return;
            }

            foreach ($item as $sub) {
                $walk($sub);
            }
        };

        foreach (($geojson['features'] ?? []) as $feature) {
            $walk($feature['geometry']['coordinates'] ?? []);
        }

        if (is_null($minLng)) {
            return null;
        }

        return [$minLng, $minLat, $maxLng, $maxLat];
    }
}