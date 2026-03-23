/* =========================
LOAD ĐỊA CHÍNH MỚI (MapLibre)
========================= */

/* =========================
HELPERS
========================= */

function isValidDcMoiNumber(value) {
    return Number.isFinite(Number(value));
}

function isValidDcMoiCoord(coord) {
    return Array.isArray(coord) &&
        coord.length >= 2 &&
        isValidDcMoiNumber(coord[0]) &&
        isValidDcMoiNumber(coord[1]);
}

function sameDcMoiCoord(a, b) {
    return isValidDcMoiCoord(a) &&
        isValidDcMoiCoord(b) &&
        Number(a[0]) === Number(b[0]) &&
        Number(a[1]) === Number(b[1]);
}

function closeDcMoiRing(ring) {
    if (!Array.isArray(ring)) return null;

    const cleanRing = ring
        .filter(isValidDcMoiCoord)
        .map(coord => [Number(coord[0]), Number(coord[1])]);

    if (cleanRing.length < 3) return null;

    if (!sameDcMoiCoord(cleanRing[0], cleanRing[cleanRing.length - 1])) {
        cleanRing.push([...cleanRing[0]]);
    }

    if (cleanRing.length < 4) return null;

    return cleanRing;
}

function normalizeDcMoiPolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const rings = coords
        .map(closeDcMoiRing)
        .filter(Boolean);

    return rings.length ? rings : null;
}

function normalizeDcMoiMultiPolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const polygons = coords
        .map(normalizeDcMoiPolygonCoords)
        .filter(Boolean);

    return polygons.length ? polygons : null;
}

function normalizeDcMoiFeature(feature) {
    if (!feature || feature.type !== "Feature" || !feature.geometry) return null;

    const geometry = feature.geometry;
    const geometryType = geometry.type;

    if (geometryType === "Polygon") {
        const polygon = normalizeDcMoiPolygonCoords(geometry.coordinates);
        if (!polygon) return null;

        return {
            ...feature,
            properties: feature.properties || {},
            geometry: {
                type: "Polygon",
                coordinates: polygon
            }
        };
    }

    if (geometryType === "MultiPolygon") {
        const multiPolygon = normalizeDcMoiMultiPolygonCoords(geometry.coordinates);
        if (!multiPolygon) return null;

        return {
            ...feature,
            properties: feature.properties || {},
            geometry: {
                type: "MultiPolygon",
                coordinates: multiPolygon
            }
        };
    }

    return null;
}

function normalizeDcMoiGeoJSON(data) {
    if (!data || !Array.isArray(data.features)) {
        return {
            type: "FeatureCollection",
            features: [],
            bbox: null
        };
    }

    const features = data.features
        .map(normalizeDcMoiFeature)
        .filter(Boolean);

    return {
        type: "FeatureCollection",
        features,
        bbox: Array.isArray(data.bbox) ? data.bbox : null
    };
}

function getDcMoiBBox(data) {
    try {
        if (Array.isArray(data?.bbox) &&
            data.bbox.length === 4 &&
            data.bbox.every(v => Number.isFinite(Number(v)))) {
            return data.bbox.map(Number);
        }

        if (typeof turf !== "undefined" && data?.features?.length) {
            return turf.bbox(data);
        }
    } catch (e) {
        console.warn("bbox dc_moi lỗi:", e);
    }

    return null;
}

/* =========================
EVENT HANDLERS
========================= */

function handleDcMoiClick(e) {
    if (!e.features || e.features.length === 0) {
        console.warn("Không có thửa tại vị trí click");
        return;
    }

    const feature = e.features[0];
    window.currentFeature = feature;

    if (typeof highlightParcel === "function") {
        highlightParcel(feature);
    }

    if (typeof showParcelInfo === "function") {
        showParcelInfo(feature);
    }

    if (typeof drawParcelMeasure === "function") {
        drawParcelMeasure(feature);
    }
}

function handleDcMoiDoubleClick(e) {
    const lng = e.lngLat.lng;
    const lat = e.lngLat.lat;

    if (typeof addMarker === "function") {
        addMarker(lat, lng);
    }
}

/* =========================
UPSERT SOURCE
========================= */

function upsertGeoJSONSource(sourceId, data) {
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

function loadDcMoi(data) {
    if (!map) {
        console.warn("Map chưa khởi tạo");
        return;
    }

    if (typeof clearMeasure === "function") {
        clearMeasure();
    }

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu địa chính mới");
        return;
    }

    const safeData = normalizeDcMoiGeoJSON(data);

    if (!safeData.features.length) {
        console.warn("Không có feature polygon hợp lệ cho địa chính mới");
        return;
    }

    /* gỡ event cũ để tránh bind lặp */
    try {
        map.off("click", "dc_moi_fill", handleDcMoiClick);
    } catch (e) {}

    try {
        map.off("dblclick", "dc_moi_fill", handleDcMoiDoubleClick);
    } catch (e) {}

    /* source */
    const isNewSource = upsertGeoJSONSource("dc_moi", safeData);

    /* layer */
    if (isNewSource) {
        map.addLayer({
            id: "dc_moi_fill",
            type: "fill",
            source: "dc_moi",
            paint: {
                "fill-color": "#ffd700",
                "fill-opacity": 0.22
            }
        });

        map.addLayer({
            id: "dc_moi_line",
            type: "line",
            source: "dc_moi",
            paint: {
                "line-color": "#ffd700",
                "line-width": [
                    "interpolate",
                    ["linear"],
                    ["zoom"],
                    10, 0.8,
                    14, 1.5,
                    18, 2.2
                ]
            }
        });
    } else {
        /* phòng khi source còn mà layer bị mất */
        if (!map.getLayer("dc_moi_fill")) {
            map.addLayer({
                id: "dc_moi_fill",
                type: "fill",
                source: "dc_moi",
                paint: {
                    "fill-color": "#ffd700",
                    "fill-opacity": 0.22
                }
            });
        }

        if (!map.getLayer("dc_moi_line")) {
            map.addLayer({
                id: "dc_moi_line",
                type: "line",
                source: "dc_moi",
                paint: {
                    "line-color": "#ffd700",
                    "line-width": [
                        "interpolate",
                        ["linear"],
                        ["zoom"],
                        10, 0.8,
                        14, 1.5,
                        18, 2.2
                    ]
                }
            });
        }
    }

    /* bind event */
    map.on("click", "dc_moi_fill", handleDcMoiClick);
    map.on("dblclick", "dc_moi_fill", handleDcMoiDoubleClick);

    /* fit bounds */
    try {
        const bbox = getDcMoiBBox(safeData);

        if (bbox) {
            map.fitBounds(
                [
                    [bbox[0], bbox[1]],
                    [bbox[2], bbox[3]]
                ],
                {
                    padding: 20,
                    duration: 800
                }
            );
        }
    } catch (e) {
        console.warn("Không thể fitBounds dc_moi:", e);
    }

    /* search */
    if (typeof initParcelSearch === "function") {
        try {
            initParcelSearch(safeData);
        } catch (e) {
            console.warn("initParcelSearch dc_moi lỗi:", e);
        }
    }
}