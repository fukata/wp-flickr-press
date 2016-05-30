#!/usr/bin/env ruby 
# ruby misc/change-version.rb PLUGIN_VERSION TESTED_VERSION REQUIRE_VERSION
# ex) ruby misc/change-version.rb 2.3.3 4.5.2 3.8

version = ARGV[0]
tested_version = ARGV[1]
require_version = ARGV[2]

raise "Please specify version x.y.z" unless version

# FlickrPress.php
if File.file?("FlickrPress.php") then
  content = File.read("FlickrPress.php").gsub(/(VERSION = )'[0-9]+\.[0-9]+\.[0-9]+'/, '\1' + "'#{version}'")
  File.open("FlickrPress.php", "w") do |f|
    f.write content
  end
else
  raise "Not found FlickrPress.php"
end

# wp-flickr-press.php
if File.file?("wp-flickr-press.php") then
  content = File.read("wp-flickr-press.php").gsub(/(Version: )[0-9]+\.[0-9]+\.[0-9]+/, '\1' + "#{version}")
  File.open("wp-flickr-press.php", "w") do |f|
    f.write content
  end
else
  raise "Not found wp-flickr-press.php"
end

# readme.txt
if File.file?("readme.txt") then
  content = File.read("readme.txt").gsub(/(Stable tag: )[0-9]+\.[0-9]+\.[0-9]+/, '\1' + "#{version}")
  content = content.gsub(/(Tested up to: )[0-9]+\.[0-9]+\.[0-9]+/, '\1' + "#{tested_version}") if tested_version
  content = content.gsub(/(Requires at least: )[0-9]+\.[0-9]+(?:\.[0-9]+)?/, '\1' + "#{require_version}") if require_version 
  File.open("readme.txt", "w") do |f|
    f.write content
  end
else
  raise "Not found readme.txt"
end

puts "Please add change log for #{version} in readme.txt"
