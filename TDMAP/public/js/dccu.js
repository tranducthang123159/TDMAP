/* =========================
LOAD ĐỊA CHÍNH CŨ
========================= */

function isMobileDevice() {
    return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
}

function loadDcCu(data) {

    clearMeasure();

    /* 🔥 SET KTT */
    updateVN2000(108.5);

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu địa chính cũ");
        return;
    }

    let safeData = JSON.parse(JSON.stringify(data));

    safeData.features = safeData.features.filter(f => {
        return (
            f &&
            f.type === "Feature" &&
            f.geometry &&
            f.geometry.type &&
            f.geometry.coordinates
        );
    });

    if (!safeData.features.length) {
        console.warn("Không có feature hợp lệ");
        return;
    }

    // Mobile bỏ simplify để tránh crash
    if (!isMobileDevice()) {
        try {
            safeData = turf.simplify(safeData, {
                tolerance: 0.00003,
                highQuality: false
            });
        } catch (e) {
            console.warn("Simplify dc_cu lỗi, dùng dữ liệu gốc:", e);
        }
    }

    /* remove event an toàn */
    if (map.getLayer("dc_cu_fill")) {
        map.off("click", "dc_cu_fill");
        map.off("dblclick", "dc_cu_fill");
    }

    /* remove layer an toàn */
    if (map.getLayer("dc_cu_fill")) map.removeLayer("dc_cu_fill");
    if (map.getLayer("dc_cu_line")) map.removeLayer("dc_cu_line");
    if (map.getSource("dc_cu")) map.removeSource("dc_cu");

    /* add source */
    map.addSource("dc_cu", {
        type: "geojson",
        data: safeData
    });

    /* polygon */
    map.addLayer({
        id: "dc_cu_fill",
        type: "fill",
        source: "dc_cu",
        paint: {
            "fill-color": "#49cbf3",
            "fill-opacity": 0.25
        }
    });

    /* border */
    map.addLayer({
        id: "dc_cu_line",
        type: "line",
        source: "dc_cu",
        paint: {
            "line-color": "#49cbf3",
            "line-width": 2
        }
    });

    /* =========================
    CLICK THỬA
    ========================= */

    map.on("click", "dc_cu_fill", function (e) {

        if (!e.features || e.features.length === 0) {
            alert("Không có thửa!");
            return;
        }

        let feature = e.features[0];

        window.currentFeature = feature;

        highlightParcel(feature);
        showParcelInfo(feature);
        drawParcelMeasure(feature);

    });

    /* =========================
    DOUBLE CLICK
    ========================= */

    map.on("dblclick", "dc_cu_fill", function (e) {

        let lng = e.lngLat.lng;
        let lat = e.lngLat.lat;

        addMarker(lat, lng);

    });

    /* =========================
    ZOOM
    ========================= */

    try {
        let bbox = turf.bbox(safeData);

        map.fitBounds([
            [bbox[0], bbox[1]],
            [bbox[2], bbox[3]]
        ], {
            padding: 20
        });
    } catch (e) {
        console.warn("Không thể fitBounds dc_cu:", e);
    }

    /* search nếu có */
 if (typeof initParcelSearch === "function") {
    try {
        initParcelSearch(safeData);
    } catch (e) {
        console.warn("initParcelSearch dc_cu lỗi:", e);
    }
}
}