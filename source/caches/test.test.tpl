<div>
    <h1>Tests</h1>
    <section>
        <h2>Variable</h2>
        <span>
        <?php echo self::get('var') ?> 
        </span>
    </section>
    <section>
        <h2>if</h2>
<?php if (true){ ?>
        <span>Passed</span>
<?php } ?>
    </section>
    <section>
        <h2>if else</h2>
<?php if (false){ ?>
        <span></span>
<?php }else{ ?>
        <span>Passed</span>
<?php } ?>
    </section>
    <section>
        <h2>Anticache</h2>
        <span>
            <?php echo self::anticache('/img/favicon.png') ?> 
        </span>
        <p>If you see something like '/img/favicon.png?1495876297', test passed.</p>
    </section>
    <section>
        <h2>for(...) cycle</h2>
        <span>
            <?php for ($i=0; $i < count(self::get('passed')); ++$i){ ?><?php echo self::get('passed')[$i] ?><?php } ?> 
        </span>
    </section>
    <section>
        <h2>foreach cycle</h2>
        <span>
            <?php foreach (self::get('passed') as &$letter){ ?><?php echo $letter ?><?php } ?> 
        </span>
    </section>
    <section>
        <h2>Include</h2>
        <span>
            <?php echo self::display('test/subtest.tpl') ?> 
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