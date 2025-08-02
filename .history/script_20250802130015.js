const form = document.getElementById("download-form");
const urlInput = document.getElementById("url-input");
const errorMsg = document.getElementById("error-message");
const loader = document.getElementById("loader");
const results = document.getElementById("results-section");
const thumbnailEl = document.getElementById("video-thumbnail");
const titleEl = document.getElementById("video-title");
const linksContainer = document.getElementById("download-links");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorMsg.classList.add("hidden");
  results.classList.add("hidden");
  loader.classList.remove("hidden");

  try {
    const url = urlInput.value.trim();
    if (!url) throw new Error("Please enter a URL.");

    const res = await fetch(`./downloader.php?url=${encodeURIComponent(url)}`, {
      method: "GET",
    });

    if (!res.ok) {
      const text = await res.text();
      console.error("HTTP Error:", res.status, text);
      throw new Error(`Server returned ${res.status}`);
    }

    const raw = await res.text();
    console.log("Raw backend response:", raw);

    let data;
    try {
      data = JSON.parse(raw);
    } catch (parseErr) {
      console.error("JSON parse error:", parseErr);
      throw new Error("Invalid JSON from server:\n" + raw);
    }

    loader.classList.add("hidden");

    if (!data.success) {
      console.error("Backend error field:", data.error);
      throw new Error(data.error || "Unknown error from backend");
    }

    thumbnailEl.src = data.thumbnail || "fallback-thumbnail.jpg";
    titleEl.textContent = data.title || "No title available";
    linksContainer.innerHTML = "";

    const filteredItems = data.items.filter(
      (item) =>
        (item.vcodec !== "none" && item.acodec !== "none") || item.type.includes("mp4")
    );

    filteredItems.sort((a, b) => (b.height || 0) - (a.height || 0));

    filteredItems.slice(0, 5).forEach((item) => {
      const a = document.createElement("a");
      a.href = item.url;
      a.target = "_blank";
      a.download = "";
      a.className =
        "w-full flex items-center justify-between px-4 py-3 text-white font-bold rounded-lg bg-green-500 hover:bg-green-600 transition-colors mb-2";
      a.innerHTML = `
        <span>${item.type || item.ext || "Download"} - ${item.resolution || ""}</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>`;
      linksContainer.appendChild(a);
    });

    results.classList.remove("hidden");
  } catch (err) {
    loader.classList.add("hidden");
    console.error("Unexpected error:", err);
    errorMsg.textContent = err.message;
    errorMsg.classList.remove("hidden");
  }
});