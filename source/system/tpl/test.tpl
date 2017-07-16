<div>
<h1>Tests</h1>
<section>
    <h2>Variable</h2>
    <span>
        {{@var}}
    </span>
</section>
<section>
    <h2>if</h2>
    {{if (true)}}
    <span>Passed</span>
    {{end}}
</section>
<section>
    <h2>if else</h2>
    {{if (false)}}
    <span></span>
    {{else}}
    <span>Passed</span>
    {{end}}
</section>
<section>
    <h2>Anticache</h2>
    <span>
        {{anticache ('/img/favicon.png')}}
    </span>
    <p>If you see something like '/img/favicon.ico?1495876297', test passed.</p>
</section>
<section>
    <h2>for(...) cycle</h2>
    <span>
        {{for ($i=0 to count(@passed))}}{{@passed[$i]}}{{end}}
    </span>
</section>
<section>
    <h2>foreach cycle</h2>
    <span>
        {{@passed as $letter}}{{$letter}}{{end}}
    </span>
</section>
<section>
    <h2>Include</h2>
    <span>
        {{include ('subtest.tpl')}}
    </span>
</section>
</div>

<style>
body, html{
    font-family: Helvetica, Arial, sans-serif;
}
div {
    max-width: 500px;
    margin: 0 auto;
}
</style>