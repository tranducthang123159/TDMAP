/* =========================
LOAD ĐỊA CHÍNH CŨ
========================= */

function isMobileDevice() {
    return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
}

/* =========================
HELPERS
========================= */

function isValidNumber(value) {
    return Number.isFinite(Number(value));
}

function isValidCoord(coord) {
    return Array.isArray(coord) && coord.length >= 2 && isValidNumber(coord[0]) && isValidNumber(coord[1]);
}

function sameCoord(a, b) {
    return isValidCoord(a) &&
        isValidCoord(b) &&
        Number(a[0]) === Number(b[0]) &&
        Number(a[1]) === Number(b[1]);
}

function closeRing(ring) {
    if (!Array.isArray(ring)) return null;

    const cleanRing = ring
        .filter(isValidCoord)
        .map(coord => [Number(coord[0]), Number(coord[1])]);

    if (cleanRing.length < 3) return null;

    if (!sameCoord(cleanRing[0], cleanRing[cleanRing.length - 1])) {
        cleanRing.push([...cleanRing[0]]);
    }

    if (cleanRing.length < 4) return null;

    return cleanRing;
}

function normalizePolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const rings = coords
        .map(closeRing)
        .filter(Boolean);

    return rings.length ? rings : null;
}

function normalizeMultiPolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const polygons = coords
        .map(normalizePolygonCoords)
        .filter(Boolean);

    return polygons.length ? polygons : null;
}

function normalizeDcCuFeature(feature) {
    if (!feature || feature.type !== "Feature" || !feature.geometry) return null;

    const geometry = feature.geometry;
    const geometryType = geometry.type;

    if (geometryType === "Polygon") {
        const polygon = normalizePolygonCoords(geometry.coordinates);
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
        const multiPolygon = normalizeMultiPolygonCoords(geometry.coordinates);
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

function normalizeDcCuGeoJSON(data) {
    if (!data || !Array.isArray(data.features)) {
        return {
            type: "FeatureCollection",
            features: []
        };
    }

    const features = data.features
        .map(normalizeDcCuFeature)
        .filter(Boolean);

    return {
        type: "FeatureCollection",
        features
    };
}

function getDcCuBBox(data) {
    try {
        if (!data || !data.features || !data.features.length) return null;
        return turf.bbox(data);
    } catch (e) {
        console.warn("bbox dc_cu lỗi:", e);
        return null;
    }
}

/* =========================
EVENT HANDLERS
========================= */

function handleDcCuClick(e) {
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

function handleDcCuDoubleClick(e) {
    const lng = e.lngLat.lng;
    const lat = e.lngLat.lat;

    if (typeof addMarker === "function") {
        addMarker(lat, lng);
    }
}

/* =========================
MAIN LOAD
========================= */

function loadDcCu(data) {
    if (!map) {
        console.warn("Map chưa khởi tạo");
        return;
    }

    if (typeof clearMeasure === "function") {
        clearMeasure();
    }

    if (typeof updateVN2000 === "function") {
        updateVN2000(108.5);
    }

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu địa chính cũ");
        return;
    }

    let safeData = normalizeDcCuGeoJSON(data);

    if (!safeData.features.length) {
        console.warn("Không có feature polygon hợp lệ cho địa chính cũ");
        return;
    }

    /* simplify chỉ dùng cho desktop và dữ liệu lớn */
    if (!isMobileDevice() && typeof turf !== "undefined" && safeData.features.length > 30) {
        try {
            safeData = turf.simplify(safeData, {
                tolerance: 0.00001,
                highQuality: false,
                mutate: false
            });
        } catch (e) {
            console.warn("Simplify dc_cu lỗi, dùng dữ liệu gốc:", e);
        }
    }

    /* remove event cũ đúng handler */
    try {
        map.off("click", "dc_cu_fill", handleDcCuClick);
    } catch (e) {}

    try {
        map.off("dblclick", "dc_cu_fill", handleDcCuDoubleClick);
    } catch (e) {}

    /* remove layer/source cũ */
    try {
        if (map.getLayer("dc_cu_line")) map.removeLayer("dc_cu_line");
    } catch (e) {
        console.warn("remove dc_cu_line lỗi:", e);
    }

    try {
        if (map.getLayer("dc_cu_fill")) map.removeLayer("dc_cu_fill");
    } catch (e) {
        console.warn("remove dc_cu_fill lỗi:", e);
    }

    try {
        if (map.getSource("dc_cu")) map.removeSource("dc_cu");
    } catch (e) {
        console.warn("remove source dc_cu lỗi:", e);
    }

    /* add source */
    map.addSource("dc_cu", {
        type: "geojson",
        data: safeData
    });

    /* fill */
    map.addLayer({
        id: "dc_cu_fill",
        type: "fill",
        source: "dc_cu",
        paint: {
            "fill-color": "#49cbf3",
            "fill-opacity": 0.18
        }
    });

    /* line */
    map.addLayer({
        id: "dc_cu_line",
        type: "line",
        source: "dc_cu",
        paint: {
            "line-color": "#49cbf3",
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

    /* bind event */
    map.on("click", "dc_cu_fill", handleDcCuClick);
    map.on("dblclick", "dc_cu_fill", handleDcCuDoubleClick);

    /* fit bounds */
    try {
        const bbox = getDcCuBBox(safeData);

        if (bbox) {
            map.fitBounds(
                [
                    [bbox[0], bbox[1]],
                    [bbox[2], bbox[3]]
                ],
                {
                    padding: isMobileDevice() ? 10 : 20,
                    duration: 800
                }
            );
        }
    } catch (e) {
        console.warn("Không thể fitBounds dc_cu:", e);
    }

    /* search */
    if (typeof initParcelSearch === "function") {
        try {
            initParcelSearch(safeData);
        } catch (e) {
            console.warn("initParcelSearch dc_cu lỗi:", e);
        }
    }
}