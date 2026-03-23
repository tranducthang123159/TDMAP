/* =========================
LOAD ĐỊA CHÍNH MỚI (MapLibre)
========================= */

function loadDcMoi(data) {
    clearMeasure();

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu địa chính mới");
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

    if (map.getLayer("dc_moi_fill")) {
        map.off("click", "dc_moi_fill");
        map.off("dblclick", "dc_moi_fill");
    }

    if (map.getSource("dc_moi")) {
        if (map.getLayer("dc_moi_fill")) map.removeLayer("dc_moi_fill");
        if (map.getLayer("dc_moi_line")) map.removeLayer("dc_moi_line");
        map.removeSource("dc_moi");
    }

    map.addSource("dc_moi", {
        type: "geojson",
        data: safeData
    });

    map.addLayer({
        id: "dc_moi_fill",
        type: "fill",
        source: "dc_moi",
        paint: {
            "fill-color": "#ffd700",
            "fill-opacity": 0.35
        }
    });

    map.addLayer({
        id: "dc_moi_line",
        type: "line",
        source: "dc_moi",
        paint: {
            "line-color": "#ffd700",
            "line-width": 2
        }
    });

    map.on("click", "dc_moi_fill", function (e) {
        if (!e.features || e.features.length === 0) {
            alert("Không có thửa!");
            return;
        }

        let feature = e.features[0];

        window.currentFeature = feature;
        console.log("CLICK xong currentFeature =", window.currentFeature);

        highlightParcel(feature);
        showParcelInfo(feature);
        drawParcelMeasure(feature);
    });

    map.on("dblclick", "dc_moi_fill", function (e) {
        let lng = e.lngLat.lng;
        let lat = e.lngLat.lat;
        addMarker(lat, lng);
    });

    try {
        let bbox = turf.bbox(safeData);

        if (
            Array.isArray(bbox) &&
            bbox.length === 4 &&
            isFinite(bbox[0]) &&
            isFinite(bbox[1]) &&
            isFinite(bbox[2]) &&
            isFinite(bbox[3])
        ) {
            map.fitBounds(
                [
                    [bbox[0], bbox[1]],
                    [bbox[2], bbox[3]]
                ],
                {
                    padding: 20
                }
            );
        }
    } catch (e) {
        console.warn("Không thể fitBounds dc_moi:", e);
    }

    if (typeof initParcelSearch === "function") {
        try {
            initParcelSearch(safeData);
        } catch (e) {
            console.warn("initParcelSearch dc_moi lỗi:", e);
        }
    }
}