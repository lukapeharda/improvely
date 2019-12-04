<?php

namespace LukaPeharda\Improvely;

use LukaPeharda\Improvely\Errors\MissingRequiredParameter;

class Conversion extends Improvely
{
    /**
     * Record a conversion with given $params.
     *
     * Either "label", "previous_reference", "subid" or "ip"/"user_agent" pair
     * is required.
     *
     * @param   array  $params
     *
     * @return  object
     */
    public function record($params)
    {
        // Either "label", "previous_reference", "subid" or "ip"/"user_agent" pair is required
        if (
            ( ! isset($params['label']) || empty($params['label']))
            && ( ! isset($params['previous_reference']) || empty($params['previous_reference']))
            && ( ! isset($params['subid']) || empty($params['subid']))
            && ( ! isset($params['ip']) || empty($params['ip']) || ! isset($params['user_agent']) || empty($params['user_agent']))
        ) {
            throw new MissingRequiredParameter("Missing required parameter: 'label', 'ip' and 'user_agent', 'previous_reference' or 'subid'.");
        }

        return $this->request(Improvely::METHOD_POST, 'conversion', $params);
    }
}