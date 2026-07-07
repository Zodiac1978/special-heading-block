# Special Heading Block

[![Build status](https://github.com/Zodiac1978/special-heading-block/actions/workflows/ci.yml/badge.svg)](https://github.com/Zodiac1978/special-heading-block/actions/workflows/ci.yml) [![Donate with PayPal](https://img.shields.io/badge/PayPal-Donate-yellow.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LCH9UVV7RKDFY)

A PHP-only heading block with gradient and outline highlighting.

## Why?

This plugin adds a `Special Heading` block without requiring a JavaScript build step.

It uses WordPress block supports for common editor controls such as alignment, colors, gradients, spacing, borders, dimensions, and typography. The block itself is registered and rendered in PHP.

The highlighted part of the heading can use the selected gradient with a custom angle, or it can be displayed as an outlined text style.

## Frequently Asked Questions

### Where do I find the block?

Add a new block in the editor and search for `Special Heading`. The block is registered in the text category.

### Can I change the heading level?

Yes. The block supports heading levels from `H1` to `H6`.

### Can I use theme gradients?

Yes. The block uses the native WordPress gradient controls. Preset gradients and custom gradients are applied to the highlighted text.

### Can I change the gradient angle?

Yes. The gradient angle can be set from `0` to `360` degrees.

### Does this plugin need a build step?

No. The block is registered with PHP and uses a small stylesheet for the gradient and outline styles.

### Can I help you?

Thanks for asking! Yes, I'm interested in edge cases and incompatibilities with themes, block styles, or editor settings.
Please open a [new issue](https://github.com/Zodiac1978/special-heading-block/issues) for this.

## Thanks

Props to the WordPress contributors working on PHP-only block registration and block supports.

## Changelog

### 1.0.0

* Initial public release
