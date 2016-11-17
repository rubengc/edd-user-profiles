== EDD User Profiles ==

In development

Important: This plugin is in development, so assets are not minified (you need to define SCRIPT_DEBUG to true)

Done:
- URL rules to match user profile pages (based on user nicename)
- New [edd_profile_editor] fields: User avatar (works with EDD FES avatar) and user description

TODO:
- Add a template for user profile and allow override using same template system as EDD
- User profile template will load tabs based on other extensions (for example, if EDD Wish List is active, add a tab with their public lists, if FES is active and user is a vendor, add a tab with published downloads)
- Load each user profile tab using ajax
- Badges? Probably in a separated extension
- Follow/Friends feature?
- Discuss more features