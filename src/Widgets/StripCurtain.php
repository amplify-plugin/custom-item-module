<?php

namespace Amplify\System\CustomItem\Widgets;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Illuminate\View\Component;

/**
 * @class StripCurtain
 */
class StripCurtain extends Component
{
    /**
     * @var array
     */
    public $options;

    public $completedUrl;

    public $replacementUrl;

    public $bulkUrl;

    public $strip;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->options = Config::get('amplify.widget.'.__CLASS__, []);
        $this->urlGenerator();
        $this->getQueryString();
    }

    /**
     * Generate strip URL
     */
    public function urlGenerator(): void
    {
        $this->completedUrl = $this->getCompletedUrl();
        $this->replacementUrl = $this->getReplacementUrl();
        $this->bulkUrl = $this->getBulkUrl();
    }

    /**
     * Get Completed section url
     */
    private function getCompletedUrl(): string
    {
        return url('/custom/strip-curtains?').http_build_query(['strip' => 'completed'], null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get replacement section url
     */
    private function getReplacementUrl(): string
    {
        return url('/custom/strip-curtains?').http_build_query(['strip' => 'replacement'], null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get bulk section url
     */
    private function getBulkUrl(): string
    {
        return url('/custom/strip-curtains?').http_build_query(['strip' => 'bulk'], null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get value from query string
     */
    private function getQueryString(): void
    {
        $this->strip = request()->query('strip');
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    /**
     * Get Strip view page according to strip query string
     */
    private function getStripView(): View
    {
        switch ($this->strip) {

            case 'completed':
                return view('custom-item::stripCurtain.completed');

            case 'replacement':
                return view('custom-item::stripCurtain.replacement');

            case 'bulk':
                return view('custom-item::stripCurtain.bulk');

            default:
                return view('custom-item::strip-curtain');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return $this->getStripView();
    }
}
