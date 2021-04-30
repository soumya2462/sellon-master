<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Studio\V2;

use Twilio\Exceptions\TwilioException;
use Twilio\ListResource;
use Twilio\Options;
use Twilio\Serialize;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
class FlowValidateList extends ListResource {
    /**
     * Construct the FlowValidateList
     *
     * @param Version $version Version that contains the resource
     * @return \Twilio\Rest\Studio\V2\FlowValidateList
     */
    public function __construct(Version $version) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array();

        $this->uri = '/Flows/Validate';
    }

    /**
     * Update the FlowValidateInstance
     *
     * @param string $friendlyName The friendly_name
     * @param string $status The status
     * @param array $definition The definition
     * @param array|Options $options Optional Arguments
     * @return FlowValidateInstance Updated FlowValidateInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update($friendlyName, $status, $definition, $options = array()) {
        $options = new Values($options);

        $data = Values::of(array(
            'FriendlyName' => $friendlyName,
            'Status' => $status,
            'Definition' => Serialize::jsonObject($definition),
            'CommitMessage' => $options['commitMessage'],
        ));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new FlowValidateInstance($this->version, $payload);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Studio.V2.FlowValidateList]';
    }
}