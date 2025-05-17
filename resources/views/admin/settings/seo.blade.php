<!-- resources/views/admin/settings/seo.blade.php -->
<x-layouts.admin>
    <x-slot name="title">SEO Settings</x-slot>
    
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.settings.seo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Global SEO Settings -->
                <x-form-section title="Global SEO Settings" description="Set default SEO values for your website.">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-form.input 
                                name="site_title" 
                                label="Site Title" 
                                :value="config('settings.site_title', config('app.name'))" 
                                required 
                                helper="The main title that appears in search engine results"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="title_separator" 
                                label="Title Separator" 
                                :value="config('settings.title_separator', '|')" 
                                required 
                                helper="Character that separates page titles and site name (e.g., '|', '-', '»')"
                            />
                        </div>
                        
                        <div>
                            <x-form.textarea 
                                name="meta_description" 
                                label="Default Meta Description" 
                                :value="config('settings.meta_description')" 
                                rows="3"
                                required
                                helper="Default description that appears in search engine results (max 160 characters)"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="meta_keywords" 
                                label="Default Meta Keywords" 
                                :value="config('settings.meta_keywords')" 
                                helper="Comma-separated list of keywords (less important for modern SEO)"
                            />
                        </div>
                    </div>
                </x-form-section>
                
                <!-- Social Media SEO -->
                <x-form-section title="Social Media SEO" description="Set up Open Graph and Twitter Card metadata for social sharing.">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-form.file-input 
                                name="og_image" 
                                label="Default Social Share Image" 
                                accept="image/*" 
                                :preview="config('settings.og_image') ? asset('storage/' . config('settings.og_image')) : null" 
                                helper="Image that appears when your site is shared on social media (1200×630px recommended)"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="twitter_site" 
                                label="Twitter Username" 
                                :value="config('settings.twitter_site')" 
                                helper="Your company's Twitter handle, including the @ symbol (e.g., @yourcompany)"
                            />
                        </div>
                        
                        <div>
                            <x-form.select 
                                name="twitter_card_type" 
                                label="Twitter Card Type" 
                                :options="[
                                    'summary' => 'Summary',
                                    'summary_large_image' => 'Summary with Large Image',
                                ]" 
                                :selected="config('settings.twitter_card_type', 'summary_large_image')" 
                                helper="Type of Twitter card to use when sharing your content"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="fb_app_id" 
                                label="Facebook App ID" 
                                :value="config('settings.fb_app_id')" 
                                helper="Your Facebook App ID (optional, for Facebook Insights)"
                            />
                        </div>
                    </div>
                </x-form-section>
                
                <!-- Robots & Indexing -->
                <x-form-section title="Robots & Indexing" description="Control how search engines interact with your website.">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-form.checkbox 
                                name="enable_indexing" 
                                label="Allow search engines to index your site" 
                                :checked="config('settings.enable_indexing', true)" 
                                helper="Uncheck to add noindex meta tag to all pages"
                            />
                        </div>
                        
                        <div>
                            <x-form.checkbox 
                                name="follow_links" 
                                label="Allow search engines to follow links" 
                                :checked="config('settings.follow_links', true)" 
                                helper="Uncheck to add nofollow meta tag to all pages"
                            />
                        </div>
                        
                        <div>
                            <x-form.textarea 
                                name="robots_txt" 
                                label="Robots.txt Content" 
                                :value="config('settings.robots_txt')" 
                                rows="6"
                                helper="Content for your robots.txt file (leave empty for default)"
                            />
                        </div>
                    </div>
                </x-form-section>
                
                <!-- Verification Codes -->
                <x-form-section title="Site Verification" description="Add verification codes for search engines and webmaster tools.">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-form.input 
                                name="google_verification" 
                                label="Google Search Console Verification" 
                                :value="config('settings.google_verification')" 
                                helper="Content of the google-site-verification meta tag"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="bing_verification" 
                                label="Bing Webmaster Tools Verification" 
                                :value="config('settings.bing_verification')" 
                                helper="Content of the msvalidate.01 meta tag"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="yandex_verification" 
                                label="Yandex Verification" 
                                :value="config('settings.yandex_verification')" 
                                helper="Content of the yandex-verification meta tag"
                            />
                        </div>
                    </div>
                </x-form-section>
                
                <!-- Structured Data -->
                <x-form-section title="Structured Data" description="Add schema.org structured data for rich search results.">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-form.select 
                                name="business_type" 
                                label="Business Type" 
                                :options="[
                                    'Organization' => 'Organization (Default)',
                                    'LocalBusiness' => 'Local Business',
                                    'Corporation' => 'Corporation',
                                    'ProfessionalService' => 'Professional Service',
                                    'GeneralContractor' => 'General Contractor',
                                    'HomeAndConstructionBusiness' => 'Home And Construction Business',
                                    'RoofingContractor' => 'Roofing Contractor',
                                ]" 
                                :selected="config('settings.business_type', 'Organization')" 
                                helper="The type of business for structured data markup"
                            />
                        </div>
                        
                        <div>
                            <x-form.textarea 
                                name="custom_schema" 
                                label="Custom Schema.org JSON-LD" 
                                :value="config('settings.custom_schema')" 
                                rows="8"
                                helper="Advanced: Add custom JSON-LD structured data (optional, leave empty if unsure)"
                            />
                        </div>
                    </div>
                </x-form-section>
                
                <!-- Google Analytics & Tracking -->
                <x-form-section title="Analytics & Tracking" description="Set up website analytics and tracking codes.">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-form.input 
                                name="google_analytics" 
                                label="Google Analytics Measurement ID" 
                                :value="config('settings.google_analytics')" 
                                helper="Your GA4 measurement ID (G-XXXXXXXXXX)"
                            />
                        </div>
                        
                        <div>
                            <x-form.input 
                                name="google_tag_manager" 
                                label="Google Tag Manager Container ID" 
                                :value="config('settings.google_tag_manager')" 
                                helper="Your GTM container ID (GTM-XXXXXXX)"
                            />
                        </div>
                        
                        <div>
                            <x-form.textarea 
                                name="head_scripts" 
                                label="Additional Head Scripts" 
                                :value="config('settings.head_scripts')" 
                                rows="6"
                                helper="Additional scripts to be included in the <head> section"
                            />
                        </div>
                        
                        <div>
                            <x-form.textarea 
                                name="body_scripts" 
                                label="Additional Body Scripts" 
                                :value="config('settings.body_scripts')" 
                                rows="6"
                                helper="Additional scripts to be included at the end of the <body> section"
                            />
                        </div>
                    </div>
                </x-form-section>
                
                <!-- Form Buttons -->
                <div class="flex justify-end">
                    <x-form.button submitText="Save SEO Settings" />
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>