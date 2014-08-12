<?php

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

function assert_equals($is, $should)
{
        if ($is != $should) {
                exit(1);
        } else {
                println('Passed!');
        }
}

function assert_url($is)
{
        if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $is)) {
                exit(1);
        } else {
                println('Passed!');
        }
}

function println($text)
{
        echo $text . "\n";
}

$googl = new \Googl\Shortener();

println('#1 - Assert that shortening http://www.google.ch results in an URL');
assert_url($googl->shorten('http://www.google.ch'));

println('#2 - Assert that expanding http://goo.gl/KSggQ resolves to http://www.google.com/');
assert_equals($googl->expand('http://goo.gl/KSggQ'), 'http://www.google.com/');

println('#3 - Assert that shortening https://www.facebook.com results in an URL');
assert_url($googl->shorten('https://www.facebook.com'));

println('#4 - Assert that expanding http://goo.gl/wCWWd resolves to http://www.php.net/');
assert_equals($googl->expand('http://goo.gl/wCWWd'), 'http://www.php.net/');

# If this point is reached, all tests have passed
println('All tests have successfully passed!');