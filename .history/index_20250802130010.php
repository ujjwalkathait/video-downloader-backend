<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Instagram Downloader</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="style.css" />
  </head>
  <body class="bg-gray-50 text-gray-800">
    <header class="bg-white shadow-sm">
      <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex-shrink-0">
            <a href="#" class="text-2xl font-bold text-blue-600">InstaSave</a>
          </div>
        </div>
      </nav>
    </header>

    <main class="py-16 sm:py-24">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1
          class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight"
        >
          Instagram Downloader
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
          Paste an Instagram post, reel, or video link below to download content instantly.
        </p>

        <form id="download-form" class="mt-10 max-w-2xl mx-auto">
          <div class="flex flex-col sm:flex-row gap-3">
            <input
              type="url"
              id="url-input"
              name="url"
              required
              class="w-full px-5 py-4 text-lg text-gray-700 bg-white border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition"
              placeholder="Paste Instagram URL here..."
            />
            <button
              type="submit"
              class="flex-shrink-0 w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300"
            >
              <svg
                class="w-6 h-6 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                ></path>
              </svg>
              Download
            </button>
          </div>
        </form>

        <div
          id="error-message"
          class="mt-4 text-red-600 font-semibold hidden"
        ></div>

        <div id="loader" class="mt-12 flex justify-center hidden">
          <div class="loader"></div>
        </div>

        <div
          id="results-section"
          class="mt-12 max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-lg text-left hidden"
        >
          <div class="flex flex-col md:flex-row gap-6">
            <div class="md:w-1/3 flex-shrink-0">
              <img
                id="video-thumbnail"
                src=""
                alt="Content Thumbnail"
                class="rounded-lg w-full h-auto object-cover bg-gray-200"
              />
            </div>
            <div class="md:w-2/3">
              <h3
                id="video-title"
                class="text-xl font-bold text-gray-900 leading-snug"
              ></h3>
              <div id="download-links" class="mt-4 space-y-3"></div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="bg-gray-800 text-white">
      <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400">© 2025 InstaSave. All Rights Reserved.</p>
      </div>
    </footer>

    <script src="script.js"></script>
  </body>
</html>
