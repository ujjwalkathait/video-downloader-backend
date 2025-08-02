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
    <!-- How-To Section -->
    <section id="how-it-works" class="py-16 sm:py-24 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-extrabold text-gray-900">
            How to Download a Video
          </h2>
          <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
            Follow these three simple steps to save any video to your device.
          </p>
        </div>
        <div class="mt-12 grid gap-10 md:grid-cols-3">
          <div class="text-center">
            <div
              class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600 mx-auto"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                ></path>
              </svg>
            </div>
            <h3 class="mt-5 text-lg font-medium text-gray-900">
              1. Copy the URL
            </h3>
            <p class="mt-2 text-base text-gray-600">
              Find the video you want to download on YouTube, Instagram, etc.,
              and copy its link.
            </p>
          </div>
          <div class="text-center">
            <div
              class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600 mx-auto"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                ></path>
              </svg>
            </div>
            <h3 class="mt-5 text-lg font-medium text-gray-900">
              2. Paste the Link
            </h3>
            <p class="mt-2 text-base text-gray-600">
              Return to our site and paste the link into the input field at the
              top of the page.
            </p>
          </div>
          <div class="text-center">
            <div
              class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600 mx-auto"
            >
              <svg
                class="w-6 h-6"
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
            </div>
            <h3 class="mt-5 text-lg font-medium text-gray-900">3. Download</h3>
            <p class="mt-2 text-base text-gray-600">
              Click the "Download" button, choose your desired quality, and the
              video will be saved to your device.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-16 sm:py-24 bg-gray-50">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          Frequently Asked Questions
        </h2>
        <div class="mt-10 space-y-6">
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg">Is this service free?</h3>
            <p class="mt-2 text-gray-600">
              Yes, our video downloader is completely free to use. The site is
              supported by advertisements.
            </p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg">
              What video quality can I download?
            </h3>
            <p class="mt-2 text-gray-600">
              We strive to provide the highest quality available from the source
              platform, often including HD (720p, 1080p) and SD options.
            </p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg">
              Is it legal to download videos?
            </h3>
            <p class="mt-2 text-gray-600">
              You should only download videos for which you have permission from
              the copyright owner. It is generally permissible to download your
              own content or publicly available, non-copyrighted media for
              personal use. Please respect copyright laws.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
      <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center flex-col sm:flex-row">
          <p class="text-gray-400">
            &copy; 2025 SaveVideo. All Rights Reserved.
          </p>
          <div class="flex space-x-6 mt-4 sm:mt-0">
            <a href="#" class="text-gray-400 hover:text-white"
              >Privacy Policy</a
            >
            <a href="#" class="text-gray-400 hover:text-white"
              >Terms of Service</a
            >
          </div>
        </div>
      </div>
    </footer>
    <footer class="bg-gray-800 text-white">
      <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400">© 2025 InstaSave. All Rights Reserved.</p>
      </div>
    </footer>

    <script src="script.js"></script>
  </body>
</html>
