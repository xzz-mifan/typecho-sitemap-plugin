<?php


class SiteMapLogic
{

    private string $header;

    private string $nodes;

    /**
     * 初始化
     * SiteMapLogic constructor.
     * @param $template
     */
    public function __construct($template)
    {
        $this->header = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $this->header .= "<?xml-stylesheet type='text/xsl' href='{$template}'?>\n";
        $this->nodes  = '';
    }

    /**
     * 设置节点
     * @param        $loc
     * @param null   $lastmod
     * @param string $changefreq
     * @param float  $priority
     */
    public function setNode($loc, $lastmod = null, $changefreq = 'always', $priority = 0.7)
    {
        $lastmod     = $lastmod ?: time() - random_int(60, 360);
        $this->nodes .= "\t<url>\n" .
            "\t\t<loc>{$loc}</loc>\n" .
            "\t\t<lastmod>" . date('Y-m-d\TH:i:s\Z', $lastmod) . "</lastmod>\n" .
            "\t\t<changefreq>{$changefreq}</changefreq>\n" .
            "\t\t<priority>{$priority}</priority>\n" .
            "\t</url>\n";
    }

    /**
     * 生成
     * @return string
     */
    public function generate(): string
    {
        $this->header .= "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\nxsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"\nxmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

        return $this->header . $this->nodes . "</urlset>";
    }

    /**
     * 输出
     */
    public function output(): void
    {
        header("Content-Type: application/xml");
        echo $this->generate();
    }

}