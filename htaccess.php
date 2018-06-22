RewriteCond %{REQUEST_URI} ^.*wp-content/uploads/vietveb_resource/.*
RewriteRule ^wp-content/uploads/(vietveb_resource/.*)$ wp-content/plugins/vietveb_server/vietveb-resoure-zip.php?file=$1 [QSA,L]
