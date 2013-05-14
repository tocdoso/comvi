    <div class="container">

      <div class='row'>

        <div class='span3'>
          <form id='test-container' action='./' method='post' class='well'>
            <ul class="nav nav-list">
              <li class='nav-header'>Files</li>

              <li>
                <div class='file-selector'></div>
              </li>

              <li>
                <p class='help-block'>
                  Tips: You can select multiple files by single-clicking them.
                  You can also use shift+click to select a range of files, or
                  ctrl+click (cmd+click) to select an entire directory.
                </p>
              </li>

              <li class='divider'></li>

              <li class='nav-header'>Options</li>
              <li>
                <label for='store_statistics' class='options-description'>
                  <i class='icon-pencil'></i>
                  Store Statistics
                </label>
                <select id='store_statistics' name='store_statistics' class='test-options'>
                  <option value='0'>No</option>
                  <option value='1' <?php if ( $store_statistics ) echo "selected='selected'"; ?>>Yes</option>
                </select>
              </li>
              <li>
                <label for='create_snapshots' class='options-description'>
                  <i class='icon-camera'></i>
                  Create Snapshots
                </label>
                <select id='create_snapshots' name='create_snapshots' class='test-options'>
                  <option value='0'>No</option>
                  <option value='1' <?php if ( $create_snapshots ) echo "selected='selected'"; ?>>Yes</option>
                </select>
              </li>
              <li>
                <label for='sandbox_errors' class='options-description'>
                  <i class='icon-exclamation-sign'></i>
                  Sandbox Errors
                </label>
                <select id='sandbox_errors' name='sandbox_errors' class='test-options'>
                  <option value='0'>No</option>
                  <option value='1' <?php if ( $sandbox_errors ) echo "selected='selected'"; ?>>Yes</option>
                </select>
              </li>
              <li>
                <label for='use_xml' class='options-description'>
                  <i class='icon-wrench'></i>
                  Use XML Config
                </label>
                <select id='use_xml' name='use_xml' class='test-options'>
                  <option value='0'>No</option>
                  <option value='1' <?php if ( $use_xml ) echo "selected='selected'"; ?>>Yes</option>
                </select>
                <p class='help-block'>
                  Note that setting this to "Yes" will cause VPU to ignore the tests selected above and use the tests specified in the XML file instead.
                </p>
              </li>

              <li class='divider'></li>


              <li class='nav-header'>Display</li>
              <li>
                <label for='sort' class='display-description'>
                  <i class='icon-tasks'></i>
                  Sort
                </label>
                <select id='sort' class='test-display'>
                  <option value='Results (asc)'>Results (asc)</option>
                  <option value='Results (desc)'>Results (desc)</option>
                  <option value='Time (asc)'>Time (asc)</option>
                  <option value='Time (desc)'>Time (desc)</option>
                </select>
              </li>

              <li>
                <span class='display-description'>
                  <i class='icon-eye-open'></i>
                  Show
                </span>
                <label for='display-failed' class='checkbox'>
                  <input type='checkbox' id='display-failed' class='display-suite' value='1' checked='checked' data-target='failed' />
                  <abbr title='Failed'>F</abbr>
                </label>
                <label for='display-incomplete' class='checkbox'>
                  <input type='checkbox' id='display-incomplete' class='display-suite' value='1' checked='checked' data-target='incomplete' />
                  <abbr title='Incomplete'>I</abbr>
                </label>
                <label for='display-skipped' class='checkbox'>
                  <input type='checkbox' id='display-skipped' class='display-suite' value='1' checked='checked' data-target='skipped' />
                  <abbr title='Skipped'>Sk</abbr>
                </label>
                <label for='display-succeeded' class='checkbox'>
                  <input type='checkbox' id='display-succeeded' class='display-suite' value='1' checked='checked' data-target='succeeded' />
                  <abbr title='Succeeded'>Su</abbr>
                </label>
              </li>

              <li class='divider'></li>

              <li class='centered'>
                <input type='hidden' name='test_files' id='test-files' />
                <button type="submit" id='run-tests' class="btn btn-primary">Run Tests</button>
              </li>

            </ul>
          </form>
        </div>

        <div id='test-output' class='span9'></div>
      </div>

    </div>

    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js'></script>
    <script src='./js/jqueryFileSelector.js'></script>
    <script src='./js/jquery.sortElements.js'></script>
    <script src='./js/bootstrap-alert.js'></script>
    <script src='./js/jquery.hotkeys.js'></script>

    <script type='text/html' id='test-results'>
      <% _.each(notifications, function(notification) { %>
        <div class="alert alert-block alert-<%= notification.type %>">
          <button type="button" class="close" data-dismiss="alert">�</button>
          <h4 class="alert-heading"><%= notification.title %></h4>
          <p class='notification-message'><%= notification.message %></p>
        </div>
      <% }) %>

      <% _.each(suites, function(suite) { %>
        <div class='suite' data-suite-status='<%= suite.status %>' data-suite-time='<%= suite.time %>'>
          <% var statusCap = suite.status.charAt(0).toUpperCase() + suite.status.slice(1); %>
          <h3><%= suite.name %></h3>
          <div class='suite-status'>
            <span class="label label-<%= suite.status %>"><%= statusCap %></span>
          </div>

          <% _.each(suite.tests, function(test) { %>
            <div class="alert alert-block alert-<%= test.status %>">
              <h4 class="alert-heading"><%= test.name %></h4>
              <% if ( test.message) { %>
              <p class='test-message'>
                <strong><%= test.message %></strong>
              </p>
              <% } %>
              <ul class='nav'>
                <li class='test-details'>
                  <em>Execution time:</em>
                  <%= test.time %>s
                </li>
                <% if ( test.output ) { %>
                <li class='test-details'>
                  <em>Debug Output:</em>
                  <pre><%= test.output %></pre>
                </li>
                <% } %>
                <% if ( test.trace ) { %>
                <li class='test-details'>
                  <em>Stack Trace:</em>
                  <pre><%= test.trace %></pre>
                </li>
                <% } %>
              </ul>
            </div>
          <% }) %>
        </div>

      <% }) %>

      <% if ( !_.isEmpty(stats) ) { %>
        <div class='row statistics'>
            <div class='span4'>
            <h3>Suite Statistics</h3>

            <h4>Failed (<%= stats.suites.failed %>/<%= stats.suites.total %>)</h4>
            <div class="progress progress-danger">
                <div class="bar" style="width: <%= stats.suites.percentFailed %>%"></div>
            </div>

            <h4>Incomplete (<%= stats.suites.incomplete %>/<%= stats.suites.total %>)</h4>
            <div class="progress progress-warning">
                <div class="bar" style="width: <%= stats.suites.percentIncomplete %>%"></div>
            </div>

            <h4>Skipped (<%= stats.suites.skipped %>/<%= stats.suites.total %>)</h4>
            <div class="progress progress-info">
                <div class="bar" style="width: <%= stats.suites.percentSkipped %>%"></div>
            </div>

            <h4>Succeeded (<%= stats.suites.succeeded %>/<%= stats.suites.total %>)</h4>
            <div class="progress progress-success">
                <div class="bar" style="width: <%= stats.suites.percentSucceeded %>%"></div>
            </div>
            </div>

            <div class='span4 offset1'>
            <h3>Test Statistics</h3>

            <h4>Failed (<%= stats.tests.failed %>/<%= stats.tests.total %>)</h4>
            <div class="progress progress-danger">
                <div class="bar" style="width: <%= stats.tests.percentFailed %>%"></div>
            </div>

            <h4>Incomplete (<%= stats.tests.incomplete %>/<%= stats.tests.total %>)</h4>
            <div class="progress progress-warning">
                <div class="bar" style="width: <%= stats.tests.percentIncomplete %>%"></div>
            </div>

            <h4>Skipped (<%= stats.tests.skipped %>/<%= stats.tests.total %>)</h4>
            <div class="progress progress-info">
                <div class="bar" style="width: <%= stats.tests.percentSkipped %>%"></div>
            </div>

            <h4>Succeeded (<%= stats.tests.succeeded %>/<%= stats.tests.total %>)</h4>
            <div class="progress progress-success">
                <div class="bar" style="width: <%= stats.tests.percentSucceeded %>%"></div>
            </div>
            </div>
        </div>
      <% } %>

      <% if ( errors.length ) { %>
        <h3>Errors</h3>
      <% } %>

      <% _.each(errors, function(error) { %>
        <div class="alert alert-block alert-failed">
          <h4 class="alert-heading"><%= error.type %></h4>
          <p class='error-message'>
            <strong><%= error.message %></strong>
          </p>
          <ul class='nav'>
            <li class='error-details'>
              <em>File:</em>
              <%= error.file %>
            </li>
            <li class='error-details'>
              <em>Line:</em>
              <%= error.line %>
            </li>
          </ul>
        </div>
      <% }) %>

    </script>

    <script>
      $(document).ready(function() {

        var runTests = function(event) {
          var $form = $('#test-container'),
              $output = $('#test-output');

          event.preventDefault();

          $output.fadeOut(300, function() {
            $output.html(
              "<div class='loader'><img src='./img/ajax-loader.gif'></div>"
            ).fadeIn(300);

            $.post($form.attr('action'), $form.serialize(), function(response) {
              var template = $("#test-results").html();

              try {
                response = $.parseJSON(response);
              } catch (e) {
                response = {
                  errors: [],
                  suites: [],
                  stats: [],
                  notifications: [{
                    type: 'failed',
                    title: 'Error Parsing Response From Server',
                    message: response
                  }]
                };
              }

              $output.fadeOut(300, function() {
                $output.html(_.template(template, {
                  errors: response.errors,
                  notifications: response.notifications,
                  suites: response.suites,
                  stats: response.stats
                }));

                $('#sort').triggerHandler('change');

                $output.fadeIn(300, function() {
                  $('.display-suite').each(function(index, element) {
                    $(element).triggerHandler('click');
                  });
                });
              });

            });
          });
        };

        $('#run-tests').click(runTests);
        $(document).bind('keydown.t', runTests);

        $('#sort').change(function() {
          switch ( $(this).val() ) {
            case 'Results (asc)':
              $('.suite').sortElements(function(a, b) {
                return $(a).attr('data-suite-status') > $(b).attr('data-suite-status');
              });
              break;
            case 'Results (desc)':
              $('.suite').sortElements(function(a, b) {
                return $(a).attr('data-suite-status') < $(b).attr('data-suite-status');
              });
              break;
            case 'Time (asc)':
              $('.suite').sortElements(function(a, b) {
                return $(a).attr('data-suite-time') > $(b).attr('data-suite-time');
              });
              break;
            case 'Time (desc)':
              $('.suite').sortElements(function(a, b) {
                return $(a).attr('data-suite-time') < $(b).attr('data-suite-time');
              });
              break;
          }
        });

        $('.display-suite').click(function() {
          var $checkbox = $(this),
              $suites = $('.suite[data-suite-status="' + $checkbox.attr('data-target') + '"]');
          if ( $checkbox.is(':checked') ) {
              $suites.fadeIn();
          } else  {
              $suites.fadeOut();
          }
        });

        $('.file-selector').fileSelector({
          callback: function() {
            var tests = '';
            $('.file.active, .directory.active').each(function() {
              tests += $(this).children('a').attr('data-path') + '|';
            });
            $('#test-files').val(tests.slice(0, -1));
          },
          root: '<?=$test_directory;?>',
          serverEndpoint: './file-list'
        });
      });
    </script>
