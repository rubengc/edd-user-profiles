== EDD User Profiles ==

In development

Done:
- URL rules to match user profile pages (based on user nicename)
- New [edd_profile_editor] fields: User avatar (works with EDD FES avatar) and user description
- Option to force Wordpress author URLS to EDD User Profiles page
- Option to redirect FES vendor shopt to EDD User Profiles page
- Option to force FES URLS to EDD User Profiles page
- Load options and functionalities if specific plugins are active

TODO:
- Add a template for user profile and allow override using same template system as EDD
- User profile template will load tabs based on other extensions (for example, if EDD Wish List is active, add a tab with their public lists, if FES is active and user is a vendor, add a tab with published downloads)
- Load each user profile tab using ajax
- Add support for BadgeOS
- Follow/Friends feature?
- Discuss more features