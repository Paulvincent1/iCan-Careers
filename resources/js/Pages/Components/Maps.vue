<script setup>
import { MglMap, MglMarker } from "@indoorequal/vue-maplibre-gl";
import { useMap } from "@indoorequal/vue-maplibre-gl";
import { MapLibreSearchControl } from "@stadiamaps/maplibre-search-box";
import { MglNavigationControl } from "@indoorequal/vue-maplibre-gl";
import "@stadiamaps/maplibre-search-box/dist/style.css";
import { ref, watch } from "vue";

let props = defineProps({
    centerProps: null,
    markedCoordinatesProps: null,
    disableSearch: null,
    disableSetMaker: null,
});

let emit = defineEmits(["update:coordinates"]);

let markCoordinates = ref(props.markedCoordinatesProps ?? [120.9842, 14.5995]);
const style = "https://tiles.openfreemap.org/styles/liberty";
let center = props.centerProps ?? [120.9842, 14.5995];
const zoom = 6;

function setPointerCursor(event) {
    event.event.target.getCanvas().style.cursor = "default";
}

function setMarker(event) {
    if (props.disableSetMaker) {
        return;
    }
    let { lng, lat } = event.event.lngLat;
    markCoordinates.value = [lng, lat];
    emit("update:coordinates", [lng, lat]);
}

let map = useMap();
const control = new MapLibreSearchControl();

watch(
    () => map.isLoaded,
    () => {
        map.map.setMaxBounds([
            [116.0, 4.5], // Southwest
            [127.0, 21.0], // Northeast
        ]);
        if (props.disableSearch) {
            return;
        }
        map.map.addControl(control, "top-left");
    },
);
</script>
<template>
    <mgl-map
        @map:mouseover="setPointerCursor"
        @map:click="setMarker"
        :map-style="style"
        :center="center"
        :zoom="zoom"
        height="500px"
    >
        <mgl-marker :coordinates="markCoordinates" color="#cc0000">
        </mgl-marker>
        <mgl-navigation-control position="bottom-left"></mgl-navigation-control>
    </mgl-map>
</template>
<style>
@import "maplibre-gl/dist/maplibre-gl.css";

.input-container .cancel {
    top: 3px !important;
}

.maplibregl-marker:hover,
.maplibregl-marker svg:hover,
.maplibregl-marker svg g g:hover {
    cursor: default;
}
</style>
