/* =========================
LOAD QUY HOẠCH (MapLibre)
========================= */

/* =========================
HELPERS
========================= */

function isValidQuyHoachNumber(value) {
    return Number.isFinite(Number(value));
}

function isValidQuyHoachCoord(coord) {
    return Array.isArray(coord) &&
        coord.length >= 2 &&
        isValidQuyHoachNumber(coord[0]) &&
        isValidQuyHoachNumber(coord[1]);
}

function sameQuyHoachCoord(a, b) {
    return isValidQuyHoachCoord(a) &&
        isValidQuyHoachCoord(b) &&
        Number(a[0]) === Number(b[0]) &&
        Number(a[1]) === Number(b[1]);
}

function closeQuyHoachRing(ring) {
    if (!Array.isArray(ring)) return null;

    const cleanRing = ring
        .filter(isValidQuyHoachCoord)
        .map(coord => [Number(coord[0]), Number(coord[1])]);

    if (cleanRing.length < 3) return null;

    if (!sameQuyHoachCoord(cleanRing[0], cleanRing[cleanRing.length - 1])) {
        cleanRing.push([...cleanRing[0]]);
    }

    if (cleanRing.length < 4) return null;

    return cleanRing;
}

function normalizeQuyHoachPolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const rings = coords
        .map(closeQuyHoachRing)
        .filter(Boolean);

    return rings.length ? rings : null;
}

function normalizeQuyHoachMultiPolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const polygons = coords
        .map(normalizeQuyHoachPolygonCoords)
        .filter(Boolean);

    return polygons.length ? polygons : null;
}

function normalizeQuyHoachOpacity(value) {
    const num = Number(value);

    if (!Number.isFinite(num)) return 0.25;
    if (num < 0) return 0.25;
    if (num > 1) return 1;

    return num;
}

function normalizeQuyHoachFeature(feature) {
    if (!feature || feature.type !== "Feature" || !feature.geometry) return null;

    const geometry = feature.geometry;
    const geometryType = geometry.type;
    const properties = feature.properties || {};

    if (geometryType === "Polygon") {
        const polygon = normalizeQuyHoachPolygonCoords(geometry.coordinates);
        if (!polygon) return null;

        return {
            ...feature,
            properties: {
                ...properties,
                fill: properties.fill || "#ff0000",
                "fill-opacity": normalizeQuyHoachOpacity(properties["fill-opacity"])
            },
            geometry: {
                type: "Polygon",
                coordinates: polygon
            }
        };
    }

    if (geometryType === "MultiPolygon") {
        const multiPolygon = normalizeQuyHoachMultiPolygonCoords(geometry.coordinates);
        if (!multiPolygon) return null;

        return {
            ...feature,
            properties: {
                ...properties,
                fill: properties.fill || "#ff0000",
                "fill-opacity": normalizeQuyHoachOpacity(properties["fill-opacity"])
            },
            geometry: {
                type: "MultiPolygon",
                coordinates: multiPolygon
            }
        };
    }

    return null;
}

function normalizeQuyHoachGeoJSON(data) {
    if (!data || !Array.isArray(data.features)) {
        return {
            type: "FeatureCollection",
            features: [],
            bbox: null
        };
    }

    const features = data.features
        .map(normalizeQuyHoachFeature)
        .filter(Boolean);

    return {
        type: "FeatureCollection",
        features,
        bbox: Array.isArray(data.bbox) ? data.bbox : null
    };
}

function getQuyHoachBBox(data) {
    try {
        if (
            Array.isArray(data?.bbox) &&
            data.bbox.length === 4 &&
            data.bbox.every(v => Number.isFinite(Number(v)))
        ) {
            return data.bbox.map(Number);
        }

        if (typeof turf !== "undefined" && data?.features?.length) {
            return turf.bbox(data);
        }
    } catch (e) {
        console.warn("bbox quy hoạch lỗi:", e);
    }

    return null;
}

/* =========================
EVENT HANDLER
========================= */

function handleQuyHoachClick(e) {
    const feature = e.features?.[0];
    if (!feature) return;

    window.currentFeature = feature;

    if (typeof highlightParcel === "function") {
        highlightParcel(feature);
    }

    if (typeof showParcelInfo === "function") {
        showParcelInfo(feature);
    }
}

/* =========================
UPSERT SOURCE
========================= */

function upsertQuyHoachSource(sourceId, data) {
    const source = map.getSource(sourceId);

    if (source) {
        source.setData(data);
        return false;
    }

    map.addSource(sourceId, {
        type: "geojson",
        data: data,
        tolerance: 0.375,
        buffer: 32
    });

    return true;
}

/* =========================
MAIN LOAD
========================= */

function loadQuyHoach(data) {
    if (!map) {
        console.warn("Map chưa khởi tạo");
        return;
    }

    if (typeof clearMeasure === "function") {
        clearMeasure();
    }

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu quy hoạch");
        return;
    }

    const safeData = normalizeQuyHoachGeoJSON(data);

    if (!safeData.features.length) {
        console.warn("Không có feature polygon hợp lệ của quy hoạch");
        return;
    }

    /* gỡ event cũ */
    try {
        map.off("click", "quyhoach_fill", handleQuyHoachClick);
    } catch (e) {}

    const created = upsertQuyHoachSource("quy_hoach", safeData);

    if (created) {
        map.addLayer({
            id: "quyhoach_fill",
            type: "fill",
            source: "quy_hoach",
            paint: {
                "fill-color": [
                    "coalesce",
                    ["get", "fill"],
                    "#ff0000"
                ],
                "fill-opacity": [
                    "coalesce",
                    ["to-number", ["get", "fill-opacity"]],
                    0.25
                ]
            }
        });

        map.addLayer({
            id: "quyhoach_line",
            type: "line",
            source: "quy_hoach",
            paint: {
                "line-color": "#333333",
                "line-width": [
                    "interpolate",
                    ["linear"],
                    ["zoom"],
                    10, 0.3,
                    14, 0.6,
                    18, 1.2
                ]
            }
        });
    } else {
        if (!map.getLayer("quyhoach_fill")) {
            map.addLayer({
                id: "quyhoach_fill",
                type: "fill",
                source: "quy_hoach",
                paint: {
                    "fill-color": [
                        "coalesce",
                        ["get", "fill"],
                        "#ff0000"
                    ],
                    "fill-opacity": [
                        "coalesce",
                        ["to-number", ["get", "fill-opacity"]],
                        0.25
                    ]
                }
            });
        }

        if (!map.getLayer("quyhoach_line")) {
            map.addLayer({
                id: "quyhoach_line",
                type: "line",
                source: "quy_hoach",
                paint: {
                    "line-color": "#333333",
                    "line-width": [
                        "interpolate",
                        ["linear"],
                        ["zoom"],
                        10, 0.3,
                        14, 0.6,
                        18, 1.2
                    ]
                }
            });
        }
    }

    /* bind event lại sạch */
    map.on("click", "quyhoach_fill", handleQuyHoachClick);

    /* fit bounds theo data thật */
    try {
        let bbox = getQuyHoachBBox(safeData);

        if (!bbox &&
            window.currentMapMeta &&
            Array.isArray(window.currentMapMeta.bbox) &&
            window.currentMapMeta.bbox.length === 4) {
            bbox = window.currentMapMeta.bbox.map(Number);
        }

        if (bbox) {
            map.fitBounds(
                [
                    [bbox[0], bbox[1]],
                    [bbox[2], bbox[3]]
                ],
                {
                    padding: 20,
                    duration: 0
                }
            );
        }
    } catch (e) {
        console.warn("Không thể fitBounds cho quy hoạch:", e);
    }
}