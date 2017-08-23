## SimpleForms for ProcessWire

![Shield: Status = Alpha](https://img.shields.io/badge/status-alpha-orange.svg) ![Shield: Version = 0.8.0](https://img.shields.io/badge/version-0.8.0-blue.svg) ![Shield: License = MIT](https://img.shields.io/github/license/mikerockett/simpleforms.svg)

> **Note:** This module is under alpha development, and *should not* be used in production environments.

**Discussion:** https://processwire.com/talk/topic/10929-developer-centric-form-processor

Documentation will be posted after stability has been reached. SimpleForms may likely be renamed to something else. Possibilities include DevForms, QuickForms, and FormCrafter.

### Tasklist

- [x] Form attachments (mostly ready; need to do some more testing to confirm)
- [x] Make AJAX optional, using standard form submission protocols (majority done)
- [x] Add translation (module) support
- [ ] Migrate existing translation technique to mirror Jumplinks 2 (unpublished) behaviour
- [x] Auto-prepend form stylesheet to HTML templates (working, but additional considerations to be made, such as being able to define which templates will not receive the stylesheet contents)
- [x] Add YAML support for form config (JSON is preferred by the module when both formats are provided)
- [ ] Add multi-lang support for config file (possibility: config.[lang-code])
- [ ] Make module configurable - allow for default form-recipient and noreply/auto-response sender, and allow for addition of email disclaimer/signature text for the purposes of importing (this would need to have support for multiple languages)
- [ ] Simple form builder, based on JSON specifications (include support for Bootstrap, Foundation, and ProcessWire InputFields)
- [ ] Template attachments for logos or social buttons in emails
- [ ] Add autoload support (SoC)
- [ ] Save submitted forms and make view-link available in emails (form-receipient only)

---

Module is released under the [MIT License](LICENSE.md).