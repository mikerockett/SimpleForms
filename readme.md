## SimpleForms for ProcessWire

This module is under heavy development, and **should not** be used in production environments, specifically due to the fact that it is not designed for production at this time - module updates will replace any forms you define.

**Current Alpha:** 0.8.0

For more information, see this forum thread:

**https://processwire.com/talk/topic/10929-developer-centric-form-processor**

### Todo

- [x] Form attachments (Mostly ready - need to do some more testing to confirm)
- [x] Make AJAX optional, using standard form submission protocols (majority done)
- [x] Add translation support
- [x] Auto-prepend form stylesheet to HTML templates (working, but additional considerations to be made, such as being able to define which templates will not receive the stylesheet contents)
- [x] Add YAML support for form config
- [ ] Add support for multi-language errors/success messages in config.json
- [ ] [in progress] Make module configurable - allow for default form-recipient and noreply/auto-response sender
- [ ] Simple form builder, based on JSON specifications (include support for Bootstrap, Foundation, and ProcessWire InputFields)
- [ ] Template attachments - for logos or social buttons in emails

### License

Module is released under the [MIT License](http://mit-license.org/)