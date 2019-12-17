<?php //strict

namespace IO\Services;

use Plenty\Plugin\Log\Loggable;

class FacetService
{
    use Loggable;
    /**
     * @param array $facets
     * @param string $type
     * @return bool
     */
    public function facetDataExists($facets, $type): bool
    {
        
        $result = false;
        
        foreach ($facets as $facet) {
            if ($facet['type'] == $type[0]) {
                $result = true;
            }
        }
        
        $this->getLogger(__METHOD__)->error('facet service', ['facets' => $facets, 'type' => $type, 'result' => $result]);

        return $result;
    }
}
