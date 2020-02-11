<?php

namespace Suitcorecms\Fields\FieldTypes;

class Map extends BasicField
{
    protected $attributes = [
        'on_index'    => false,
        'orderable'   => false,
        'searchable'  => false,
    ];

    public function formBuild($builder, $value = null, $newName = null)
    {
        $formPlugin = config('suitcorecms.fields.map.form');

        return call_user_func_array($formPlugin, [$this, $builder, $value, $newName]);
    }

    public function showOutput($model, $value)
    {
        $showPlugin = config('suitcorecms.fields.map.show');

        return call_user_func_array($showPlugin, [$this, $model, $value]);
    }

    public static function formMapBox($obj, $builder, $value = null, $newName = null)
    {
        $apiKey = config('suitcorecms.services.mapbox.apikey');
        $name = $obj->attributes['name'];
        $lat = $obj->attributes['attributes']['input-lat'] ?? null;
        $lng = $obj->attributes['attributes']['input-lng'] ?? null;
        $zoom = $obj->attributes['attributes']['input-zoom'] ?? null;
        $model = $builder->getModel();
        $valLat = $model->{$lat} ?? 0;
        $valLng = $model->{$lng} ?? 0;
        $valZoom = $model->{$zoom} ?? 4;

        return <<<HTML
            <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.js'></script>
            <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.css' rel='stylesheet' />

            <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.4.1/mapbox-gl-geocoder.min.js'></script>
            <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.4.1/mapbox-gl-geocoder.css' type='text/css' />

            <div id='map{$name}' style='width: 100%; height: 300px;'></div>
            <input type="hidden" id="lat{$name}" name="{$lat}" value="{$valLat}">
            <input type="hidden" id="lng{$name}" name="{$lng}" value="{$valLng}">
            <input type="hidden" id="zoom{$name}" name="{$zoom}" value="{$valZoom}">

            <script type="text/javascript">
                mapboxgl.accessToken = '{$apiKey}';
                var map{$name} = new mapboxgl.Map({
                    container: 'map{$name}',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [{$valLng}, {$valLat}],
                    zoom: {$valZoom}
                });

                var geocoder{$name} = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    mapboxgl: mapboxgl,
                    marker: null
                })

                var marker{$name} = new mapboxgl.Marker({
                    draggable: true
                })
                .setLngLat([{$valLng}, {$valLat}])
                .addTo(map{$name});

                function onDragEnd() {
                    var lngLat = marker{$name}.getLngLat();
                    document.getElementById('lat{$name}').value = lngLat.lat;
                    document.getElementById('lng{$name}').value = lngLat.lng;
                }

                marker{$name}.on('dragend', onDragEnd);
                map{$name}.addControl(geocoder{$name});

                geocoder{$name}.on('result', function(results) {
                    let setLat = results.result.center[0]
                    let setLng = results.result.center[1]
                    marker{$name}.setLngLat(results.result.center)
                })

                map{$name}.on('zoom', e => {
                    document.getElementById('zoom{$name}').value = map{$name}.getZoom()
                })

            </script>
HTML;
    }

    public static function showMapBox($obj, $model, $value)
    {
        $apiKey = config('suitcorecms.services.mapbox.apikey');
        $name = $obj->attributes['name'];
        $lat = $obj->attributes['attributes']['input-lat'] ?? null;
        $lng = $obj->attributes['attributes']['input-lng'] ?? null;
        $zoom = $obj->attributes['attributes']['input-zoom'] ?? null;
        $valLat = $model->{$lat} ?? 0;
        $valLng = $model->{$lng} ?? 0;
        $valZoom = $model->{$zoom} ?? 4;

        return <<<HTML
            <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.js'></script>
            <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.css' rel='stylesheet' />

            <div id='map{$name}' style='width: 100%; height: 300px;'></div>

            <script type="text/javascript">
                mapboxgl.accessToken = '{$apiKey}';
                var map{$name} = new mapboxgl.Map({
                    container: 'map{$name}',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [{$valLng}, {$valLat}],
                    zoom: {$valZoom}
                });

                var marker{$name} = new mapboxgl.Marker({
                    draggable: false
                })
                .setLngLat([{$valLng}, {$valLat}])
                .addTo(map{$name});

            </script>
HTML;
    }
}
