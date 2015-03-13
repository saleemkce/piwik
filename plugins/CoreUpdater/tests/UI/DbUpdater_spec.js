/*!
 * Piwik - free/libre analytics platform
 *
 * Installation screenshot tests.
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

describe("DbUpdater", function () {
    this.timeout(0);

    this.fixture = "Piwik\\Plugins\\CoreUpdater\\Tests\\Fixtures\\DbUpdaterTestFixture";

    before(function () {
        testEnvironment.tablesPrefix = 'piwik_';
        testEnvironment.save();
    });

    it("should start the updater when an old version of Piwik is detected in the DB", function (done) {
        expect.screenshot("main").to.be.capture(function (page) {
            page.load("");
            page.evaluate(function () {
                $('p').each(function () {
                    var replace = $(this).html().replace(/(?!1\.0)\d+\.\d+(\.\d+)?([\-a-z]*\d+)?/g, '');
                    $(this).html(replace);
                });
            });
        }, done);
    });

    it("should show the donation form when the update process is complete", function (done) {
        expect.screenshot("updated").to.be.capture(function (page) {
            page.click('.submit');
        }, done);
    });
});
