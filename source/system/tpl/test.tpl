<h1>Тесты</h1>
<ul>
{{if (true)}}
<li>Тест if пройден</li>
{{end}}

{{if (false)}}
<li>Тест if else не пройден</li>
{{else}}
<li>Тест if else пройден</li>
{{end}}

{{for ($i=0 to 1)}}
<li>Тест for пройден</li>
{{end}}
</ul>

{{switch (@uri)}}
{{case ('/test')}}
<p>Тест switch пройден</p>
{{break}}
{{end}}
