<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'body' => '<h2>About PakGold Rates</h2>
<p>PakGold Rates is Pakistan\'s premier platform for real-time gold and silver price tracking. We provide accurate, up-to-the-minute pricing information for precious metals in the Pakistani market.</p>
<p>Our mission is to empower buyers, sellers, and investors with transparent and reliable gold and silver rate information. Whether you are looking to buy gold jewelry, invest in precious metals, or simply stay informed about market trends, PakGold Rates is your trusted source.</p>
<h3>What We Offer</h3>
<ul>
<li>Real-time gold and silver prices in Pakistani Rupees</li>
<li>Multiple karat options including 24K, 22K, 21K, and 18K</li>
<li>Prices in various units: Tola, 10 Grams, Per Gram, and Per Ounce</li>
<li>International gold price tracking</li>
<li>Currency exchange rate information</li>
<li>Historical price data and trends</li>
</ul>
<p>We source our data from multiple reliable sources to ensure accuracy and timeliness of the information we provide.</p>',
                'meta_title' => 'About Us - PakGold Rates',
                'meta_description' => 'Learn about PakGold Rates, Pakistan\'s trusted source for real-time gold and silver prices.',
                'is_published' => true,
            ],
            [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'body' => '<h2>Disclaimer</h2>
<p>The information provided on PakGold Rates is for general informational purposes only. While we strive to keep the information up to date and accurate, we make no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, suitability, or availability with respect to the website or the information, products, services, or related graphics contained on the website for any purpose.</p>
<p>Any reliance you place on such information is therefore strictly at your own risk. In no event will we be liable for any loss or damage including without limitation, indirect or consequential loss or damage, or any loss or damage whatsoever arising from loss of data or profits arising out of, or in connection with, the use of this website.</p>
<h3>Price Information</h3>
<p>Gold and silver prices displayed on this website are indicative and may vary from actual market prices. Prices are sourced from various third-party providers and may be subject to delays. Always verify prices with your local dealer before making any purchase or sale decisions.</p>
<h3>Not Financial Advice</h3>
<p>The content on this website does not constitute financial, investment, or trading advice. We recommend consulting with a qualified financial advisor before making any investment decisions related to precious metals.</p>',
                'meta_title' => 'Disclaimer - PakGold Rates',
                'meta_description' => 'Read the disclaimer for PakGold Rates. Understand the terms of use for our gold and silver price information.',
                'is_published' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'body' => '<h2>Privacy Policy</h2>
<p>At PakGold Rates, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>
<h3>Information We Collect</h3>
<p>We may collect information about you in a variety of ways. The information we may collect on the website includes:</p>
<ul>
<li><strong>Personal Data:</strong> Name, email address, phone number, and other contact information you voluntarily provide when using our contact form or creating an account.</li>
<li><strong>Usage Data:</strong> Information about how you use our website, including your IP address, browser type, operating system, access times, and pages viewed.</li>
<li><strong>Cookies:</strong> We use cookies and similar tracking technologies to track activity on our website and hold certain information.</li>
</ul>
<h3>How We Use Your Information</h3>
<p>We use the information we collect to:</p>
<ul>
<li>Provide, operate, and maintain our website</li>
<li>Improve and personalize your experience</li>
<li>Respond to your comments, questions, and provide customer service</li>
<li>Send you updates and notifications about price changes (if opted in)</li>
<li>Monitor and analyze usage and trends</li>
</ul>
<h3>Data Security</h3>
<p>We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
<h3>Contact Us</h3>
<p>If you have any questions about this Privacy Policy, please contact us through our contact page.</p>',
                'meta_title' => 'Privacy Policy - PakGold Rates',
                'meta_description' => 'Read the privacy policy for PakGold Rates. Learn how we collect, use, and protect your personal information.',
                'is_published' => true,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-and-conditions',
                'body' => '<h2>Terms & Conditions</h2>
<p>Welcome to PakGold Rates. By accessing and using this website, you accept and agree to be bound by the terms and provisions of this agreement.</p>
<h3>Use of Website</h3>
<p>You may use this website for lawful purposes only. You must not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of the website.</p>
<h3>Intellectual Property</h3>
<p>Unless otherwise stated, PakGold Rates owns the intellectual property rights for all material on this website. All intellectual property rights are reserved. You may access content from PakGold Rates for your own personal use subject to restrictions set in these terms and conditions.</p>
<h3>Accuracy of Information</h3>
<p>While we strive to provide accurate and up-to-date gold and silver pricing information, we do not warrant that the information on this website is accurate, complete, or current. The material on this website is provided for general information only and should not be relied upon or used as the sole basis for making decisions.</p>
<h3>Limitation of Liability</h3>
<p>In no event shall PakGold Rates, nor any of its officers, directors, and employees, be held liable for anything arising out of or in any way connected with your use of this website. PakGold Rates shall not be held liable for any indirect, consequential, or special liability arising out of or in any way related to your use of this website.</p>
<h3>Changes to Terms</h3>
<p>PakGold Rates reserves the right to revise these terms and conditions at any time. By using this website, you are expected to review these terms on a regular basis.</p>
<h3>Governing Law</h3>
<p>These terms and conditions are governed by and construed in accordance with the laws of Pakistan, and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>',
                'meta_title' => 'Terms & Conditions - PakGold Rates',
                'meta_description' => 'Read the terms and conditions for using PakGold Rates website.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
