<script setup>
import { MglMap, MglMarker, MglNavigationControl } from "@indoorequal/vue-maplibre-gl";
import { useMap } from "@indoorequal/vue-maplibre-gl";
import { MapLibreSearchControl } from "@stadiamaps/maplibre-search-box";
import "@stadiamaps/maplibre-search-box/dist/style.css";
import { ref, watch, onMounted } from "vue";

let props = defineProps({
    centerProps: null,
    markedCoordinatesProps: null,
    disableSearch: null,
    disableSetMaker: null,
    initialCoordinates: {
        type: Array,
        default: () => [120.9842, 14.5995]
    }
});

let emit = defineEmits(["update:coordinates"]);

let markCoordinates = ref(props.initialCoordinates);
const style = "https://tiles.openfreemap.org/styles/liberty";
let center = props.centerProps ?? props.initialCoordinates;
const zoom = 6;

// Update markCoordinates when initialCoordinates prop changes
watch(() => props.initialCoordinates, (newCoords) => {
    if (newCoords && newCoords.length === 2) {
        markCoordinates.value = newCoords;
    }
});

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
        if (map.map) {
            map.map.setMaxBounds([
                [116.0, 4.5], // Southwest
                [127.0, 21.0], // Northeast
            ]);
            if (props.disableSearch) {
                return;
            }
            map.map.addControl(control, "top-left");
        }
    }
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