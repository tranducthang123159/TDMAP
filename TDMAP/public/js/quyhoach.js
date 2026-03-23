/* =========================
LOAD QUY HOẠCH (MapLibre)
========================= */

function loadQuyHoach(data) {
    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu quy hoạch");
        return;
    }

    // clone dữ liệu gốc
    let safeData = JSON.parse(JSON.stringify(data));

    // lọc feature rỗng / geometry lỗi cơ bản
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

    // chỉ simplify nếu được, lỗi thì bỏ qua
    // try {
    //     safeData = turf.simplify(safeData, {
    //         tolerance: 0.00003,
    //         highQuality: false
    //     });
    // } catch (e) {
    //     console.warn("Simplify lỗi, dùng dữ liệu gốc:", e);
    // }

    /* tránh click nhân đôi */
    if (map.getLayer("quyhoach_fill")) {
        map.off("click", "quyhoach_fill");
    }

    /* xóa layer cũ */
    if (map.getLayer("quyhoach_fill")) {
        map.removeLayer("quyhoach_fill");
    }

    if (map.getLayer("quyhoach_line")) {
        map.removeLayer("quyhoach_line");
    }

    if (map.getSource("quy_hoach")) {
        map.removeSource("quy_hoach");
    }

    /* add source */
    map.addSource("quy_hoach", {
        type: "geojson",
        data: safeData
    });

    /* polygon fill */
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

    /* border */
    map.addLayer({
        id: "quyhoach_line",
        type: "line",
        source: "quy_hoach",
        paint: {
            "line-color": "#333333",
            "line-width": 0.5
        }
    });

    /* zoom tới layer */
    try {
        let bbox = turf.bbox(safeData);

        map.fitBounds([
            [bbox[0], bbox[1]],
            [bbox[2], bbox[3]]
        ], {
            padding: 20
        });
    } catch (e) {
        console.warn("Không thể fitBounds cho quy hoạch:", e);
    }

    if (typeof initParcelSearch === "function") {
        initParcelSearch(safeData);
    }
}