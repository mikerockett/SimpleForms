## SimpleForms for ProcessWire

This module is under heavy development, and **should not** be used in production environments, specifically due to the fact that it is not designed for production at this time - module updates will replace any forms you define.

**Current Alpha:** 0.7.0

For more information, see this forum thread:
**https://processwire.com/talk/topic/10929-developer-centric-form-processor**

### Todo

- [x] Form attachments (Mostly ready - need to do some more testing to confirm)
- [x] Make AJAX optional, using standard form submission protocols (majority done)
- [x] Add translation support
- [x] Auto-prepend form stylesheet to HTML templates (working, but additional considerations to be made, such as being able to define which templates will not receive the stylesheet contents)
- [ ] Add support for multi-language errors/success messages in config.json
- [ ] Make module configurable - allow for default form-recipient and noreply/auto-response sender
- [ ] Simple form builder, based on JSON specifications (include support for Bootstrap, Foundation, and ProcessWire InputFields)
- [ ] Template attachments - for logos or social buttons in emails

### License

Module is released under the [MIT License](http://mit-license.org/)

```
Copyright (c) 2015, Mike Rockett. All Rights Reserved.

The MIT License (MIT)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the “Software”),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
IN THE SOFTWARE.
```