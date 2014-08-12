<?php

namespace Googl;

/**
 * This file is part of googl-php
 *
 * https://github.com/sebi/googl-php
 *
 * googl-php is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Shortener
{
        public $extended;
        private $target;
        private $apiKey;
        private $ch;

        private static $buffer = array();

        public function __construct($apiKey = null)
        {
                # Extended output mode
                $extended = false;

                # Set Google Shortener API target
                $this->target = 'https://www.googleapis.com/urlshortener/v1/url?';

                # Set API key if available
                if ($apiKey != null) {
                        $this->apiKey = $apiKey;
                        $this->target .= 'key=' . $apiKey . '&';
                }

                # Initialize cURL
                $this->ch = curl_init();
                # Set our default target URL
                curl_setopt($this->ch, CURLOPT_URL, $this->target);
                # We don't want the return data to be directly outputted, so set RETURNTRANSFER to true
                curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        }

        public function shorten($url, $extended = false)
        {
                # Check buffer
                if (!$extended && !$this->extended && !empty(self::$buffer[$url])) {
                        $result = self::$buffer[$url];
                } else {
                        # Payload
                        $data = array('longUrl' => $url);
                        $data_string = '{ "longUrl": "' . $url . '" }';

                        # Set cURL options
                        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($this->ch, CURLOPT_POST, count($data));
                        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data_string);
                        curl_setopt($this->ch, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));

                        $response = curl_exec($this->ch);

                        if ($response === false) {
                                throw new \Exception("Error occurred: " . curl_error($this->ch));
                        }

                        $decoded = json_decode($response);

                        if ($extended || $this->extended) {
                                $result = $decoded;
                        } else {
                                if (!property_exists($decoded, 'id')) {
                                        throw new \Exception("Property 'id' is not defined. Response is: " . json_encode($decoded));
                                }

                                $id = $decoded->id;
                                self::$buffer[$url] = $id;
                                $result = $id;
                        }
                }

                return $result;
        }

        public function expand($url, $extended = false)
        {
                # Set cURL options
                curl_setopt($this->ch, CURLOPT_HTTPGET, true);
                curl_setopt($this->ch, CURLOPT_URL, $this->target . 'shortUrl=' . $url);

                if ($extended || $this->extended) {
                        return json_decode(curl_exec($this->ch));
                } else {
                        return json_decode(curl_exec($this->ch))->longUrl;
                }
        }

        public function __destruct()
        {
                # Close the curl handle
                curl_close($this->ch);
                # Nulling the curl handle
                $this->ch = null;
        }
}

?>