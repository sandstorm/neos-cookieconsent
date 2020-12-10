# Sandstorm CookieConsent


THIS PACKAGE IS NO LONGER MAINTANED

You should have a look at our [CookiePunch Package](https://github.com/sandstorm/Sandstorm.CookiePunch)


This package helps you create a cookie consent that is more fine-grained than most others.

- Can be configured by the integrator
- All text elements of the popup can be customized by the editor of your site (for the parts that the integrator chooses to expose towards the neos editor through the backend ui)
- Multi-Language compatible (automatic translation of all labels in the modal and customizable texts through Neos content dimensions)
- Easily extensible -> Apps can simply be added and configured as nodetypes


# Table of contents

<!-- vim-markdown-toc GFM -->

* [Compatibility and Maintenance](#compatibility-and-maintenance)
* [Quickstart](#quickstart)
  * [1. Add your own derived Node Type.](#1-add-your-own-derived-node-type)
  * [2. Add the Node Type somewhere to your page](#2-add-the-node-type-somewhere-to-your-page)
  * [3. Add rendering for your Node Type](#3-add-rendering-for-your-node-type)
  * [4. Set the privacy policy link](#4-set-the-privacy-policy-link)
  * [5. Translate the modal (optional)](#5-translate-the-modal-optional)
  * [6. Add apps that need a users consent](#6-add-apps-that-need-a-users-consent)
  * [7. Place your new Component in your markup](#7-place-your-new-component-in-your-markup)
  * [8. Placing the Cookie-Manager button](#8-placing-the-cookie-manager-button)
* [Customization](#customization)
  * [1. Thing to customize](#1-thing-to-customize)
  * [2. Thing to customize](#2-thing-to-customize)
* [Next steps / possible further development](#next-steps--possible-further-development)
* [License &amp; Copyright](#license-amp-copyright)

<!-- vim-markdown-toc -->


# Compatibility and Maintenance

This package is currently being maintained for Neos 4.3 LTS. It is stable, we use it in our projects.

| Neos / Flow Version        | Sandstorm.CookieConsent Version | Maintained |
|----------------------------|---------------------------------|------------|
| Neos 4.3 LTS, Flow 5.3 LTS | 1.x                             | Yes        |


# Quickstart

## 1. Add your own derived Node Type.

Here we decided to place the link to your privacy policy in the inspector of the node type of CookieConsent.
We also specify a tab and a group in which it will appear.

```yaml
'Vendor.Site:CookieConsent':
  superTypes:
    'Sandstorm.CookieConsent:CookieConsent': true
  ui:
    inspector:
      tabs:
        general:
          label: Einstellungen
          position: 1
          icon: icon-pencil
      groups:
        settingsPrivacyPolicy:
          label: Datenschutzerklärung
          tab: general
          icon: ellipsis-h
          collapsed: true
  properties:
    privacyPolicyHref:
      type: string
      ui:
        label: Verlinkte Datenschutzseite
        inspector:
          group: settingsPrivacyPolicy
          editor: Neos.Neos/Inspector/Editors/LinkEditor
```

## 2. Add the Node Type somewhere to your page

For example as child node:

```yaml
'Vendor.Site:RootPage':
  superTypes:
    'Neos.Neos:Document': true
  childNodes:
    cookie-consent:
      type: 'Vendor.Site:CookieConsent'
      ui:
        label: Cookie Consent
```

## 3. Add rendering for your Node Type

This package provides you with a rendering component that takes care of loading the javascript and css needed. It also configures the library so it knows about any apps for which you need a consent by the user.

Your fusion component acts as the integrational component, `Sandstorm.CookieConsent:Component.CookieConsent` is the presentational component.

A simplified example:

```
prototype(Vendor.Site:CookieConsent) < prototype(Neos.Fusion:Component) {
    _privacyPolicyHref = /* TODO */
    _privacyPolicyHref.@process.convert = Neos.Neos:ConvertUris

    _cookieModalTranslations = /* TODO */
    _language = /* TODO */
    _apps = /* TODO */

    renderer = Sandstorm.CookieConsent:Component.CookieConsent {
        privacyPolicyHref = ${props._privacyPolicyHref}
        cookieModalTranslations = ${props._cookieModalTranslations}
        language = ${props._language}
        apps = ${props._apps}
        includeLibraryCss = true
    }
}
```

## 4. Set the privacy policy link
In this example it was decided to let the editor change the privacy policy link in the Neos inspector.

The example assumes you placed it directly below your root page (= site-node). Make sure to adapt to your node paths depending on where you placed the node.

```
prototype(Vendor.Site:CookieConsent) < prototype(Neos.Fusion:Component) {
    _privacyPolicyHref = ${q(site).find('cookie-consent').get(0).properties.privacyPolicyHref}
    _privacyPolicyHref.@process.convert = Neos.Neos:ConvertUris

    _cookieModalTranslations = /* TODO */
    _apps = /* TODO */
    _language = /* TODO */

    renderer = Sandstorm.CookieConsent:Component.CookieConsent {
        privacyPolicyHref = ${props._privacyPolicyHref}
        cookieModalTranslations = ${props._cookieModalTranslations}
        language = ${props._language}
        apps = ${props._apps}
        includeLibraryCss = true
    }
}
```

## 5. Translate the modal (optional)
The cookie consent can be translated. It has reasonable defaults for many languages and will try to determine the language of
 your page automatically (for example by looking at your `<html lang>` attribute.
 For available translations see [the github repo](https://github.com/KIProtect/klaro/tree/master/src/translations).

Note the fusion key `_cookieModalTranslations`. It reads more properties from the CookieConsent nodetype that we defined in step 1.
One will be added as an example below. You can also provide static strings and not let the editor change the keys in the inspector in the
Neos backend - it's up to you. Here the editor is supposed to provide their own texts.

```
prototype(Vendor.Site:CookieConsent) < prototype(Neos.Fusion:Component) {
    @context._cookieConsentProps = ${q(site).find('cookie-consent').get(0).properties}

    _privacyPolicyHref = ${_cookieConsentProps.privacyPolicyHref}
    _privacyPolicyHref.@process.convert = Neos.Neos:ConvertUris

    _cookieModalTranslations = Neos.Fusion:DataStructure {
        ok = ${_cookieConsentProps.ok}
        decline = ${_cookieConsentProps.decline}
        consentNotice = Neos.Fusion:DataStructure {
            description = ${_cookieConsentProps.consentNoticeDescription}
            learnMore = ${_cookieConsentProps.consentNoticeLearnMore}
        }
        consentModal = Neos.Fusion:DataStructure {
            title = ${_cookieConsentProps.consentModalTitle}
            description = ${_cookieConsentProps.consentModalDescription}
            privacyPolicy = Neos.Fusion:DataStructure {
                name = ${_cookieConsentProps.consentModalPrivacyPolicyName}
                text = ${_cookieConsentProps.consentModalPrivacyPolicyText}
            }
        }
        # Static labels, will not be translated. Hopefully generic enough to be used in any language
        purposes = Neos.Fusion:DataStructure {
            livechat = 'Live Chat'
            styling = 'Styling'
            tracking = 'Tracking'
        }
    }

    _apps = /* TODO */
    _language = /* TODO */

    renderer = Sandstorm.CookieConsent:Component.CookieConsent {
        privacyPolicyHref = ${props._privacyPolicyHref}
        cookieModalTranslations = ${props._cookieModalTranslations}
        language = ${props._language}
        apps = ${props._apps}
        includeLibraryCss = true
    }
}
```

```yaml
'Vendor.Site:CookieConsent':
  superTypes:
    'Sandstorm.CookieConsent:CookieConsent': true
  ui:
    inspector:
      groups:
        smallPopup:
          label: Kleines Popup
          tab: general
          icon: icon-pencil
          position: 100
          collapsed: true
      /* ... */
  properties:
    ok:
      type: string
      defaultValue: Alle Cookies zulassen
      ui:
        label: Cookies-Zulassen Button
        inspector:
          group: smallPopup
  /* Add all translations that should be translated by the editor ... */
```

The easiest way to find out what you can translate is to change the language of the cookieConsent to something that does not exist,
e.g. "foo" (see in the renderer of the following component, there the language key is passed as a property). In the frontend it
will then fail to load translations and tell you the path where it was looking for the translation.


## 6. Add apps that need a users consent

The package comes with a few apps (e.g. [Google Analytics](https://github.com/sandstorm/neos-cookieconsent/blob/master/Configuration/NodeTypes.CookieConsent.App.GoogleAnalytics.yaml) liable for consent already preconfigured that you can just add from the Neos backend to the collection of app below your CookieConsent node.

In case you need an app that's not yet included, simply copy one of the [existing node types](https://github.com/sandstorm/neos-cookieconsent/tree/master/Configuration) and fill in the necessary information.

**Please open a pull request or an issue with your apps configuration so others can benefit from it :)**

```
prototype(Vendor.Site:CookieConsent) < prototype(Neos.Fusion:Component) {
    @context._cookieConsentProps = /* Previous example */

    _privacyPolicyHref = /* Previous example */
    _cookieModalTranslations = /* Previous example */

    _apps = ${q(site).find('cookie-consent/apps').children().get()}

    _language = /* TODO */

    renderer = Sandstorm.CookieConsent:Component.CookieConsent {
        privacyPolicyHref = ${props._privacyPolicyHref}
        cookieModalTranslations = ${props._cookieModalTranslations}
        language = ${props._language}
        apps = ${props._apps}
        includeLibraryCss = true
    }
}
```

## 7. Place your new Component in your markup

Render your new component somewhere on your page.

`body.some.fusion.path.cookieConsent = Vendor.Site:CookieConsent`


## 8. Placing the Cookie-Manager button

To allow your users to revisit their cookie settings, you need to place a special button
component somewhere on your site.
We already provide a NodeType and its respective fusion component for this.
Simply allow `'Sandstorm.CookieConsent:OpenCookieManagerButton': true` in one of your content
collection configurations and add the component to your site (preferably on your data policy page).


# Customization

TODO

## 1. Add additional configuration for klaro.js

Add `additionalConfig` property to renderer of `Sandstorm.CookieConsent:Component.CookieConsent`. This  will add/override
properties to/of Sandstorm.CookieConsent:Klaro.Settings. You can add as many configuration options as you like.
Take in look into https://github.com/KIProtect/klaro/blob/master/dist/config.js for valid config options.

```
prototype(Your.Package:Content.CookieConsent) < prototype(Neos.Neos:ContentComponent) {
    node = ${q(site).children('cookie-consent').get(0)}

    @context {
        node = ${this.node}
        appNodes = ${q(Neos.Node.nearestContentCollection(this.node, 'apps')).children()}
    }

    renderer = Sandstorm.CookieConsent:Component.CookieConsent {

        # The additional config will add/override properties to/of Sandstorm.CookieConsent:Klaro.Settings
        additionalConfig = Neos.Fusion:DataStructure {
            acceptAll = ${q(appNodes).count() > 0}
            hideDeclineAll = ${q(appNodes).count() == 0 || (q(appNodes).count() > 0 && q(node).property('hideDeclineAll'))}
            cookieName = ${!String.isBlank(q(node).property('cookieName')) ? q(node).property('cookieName') : 'klaro'}
            cookieExpiresAfterDays = ${!String.isBlank(q(node).property('cookieExpiresAfterDays')) ? q(node).property('cookieExpiresAfterDays') : 365}
            default = ${q(node).property('default')}
            mustConsent = ${q(node).property('mustConsent')}
            storageMethod = ${q(node).property('storageMethod')}
        }
    }
}
```

## 2. Thing to customize


# Next steps / possible further development



# License &amp; Copyright

MIT-Licensed, (c) Sandstorm Media GmbH 2020
