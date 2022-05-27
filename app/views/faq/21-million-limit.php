<div class="mb-3">
    <a href="<?=$this->url('home', 'index')?>">Home</a> |
    <a href="<?=$this->url('faq', 'index')?>">FAQ</a>
</div>

<div class="mb-3">
    <div>Next: <a href="<?=$this->url('faq', 'bitcoin_is_myspace')?>">Is Bitcoin like MySpace - will it be replaced by the next hot cryptocurrency?</a></div>
</div>

<div>
    <h3 class="mb-3">What Ensures the 21 Million Bitcoin Limit?</h3>

    <p>The Bitcoin network is made up of thousands of individuals running software on their computers, referred to as “nodes”. Bitcoin is fundamentally different from most software to which we're accustomed in two key ways: other software is centrally controlled, and Bitcoin software is beholden to consensus rules.</p>

    <ol>
        <li>Central Control: Most software is centrally controlled by the company that develops it, such as Microsoft, Google, etc. Users cannot examine or modify the code, and have little or no input on design decisions or new features. Conversely, Bitcoin is open source which means every line of code can be (and has been) scrutinized. So Bitcoin users know, or at least can know, exactly what set of rules their individual node abides by, and they have complete control over what code is run on their computers.</li>
        <li>Consensus Rules: For Bitcoin software to be useful it must be part of a network composed of other users running software that abide by the same consensus rules. Consensus rules define the validity of blocks and the transactions they include. A node following different rules will be excluded from the rest of the network. This differs from other software where the incentive to remain in consensus is limited or non-existent.</li>
    </ol>

    <p>How do these two points relate to the 21 million limit? This limit is defined by a consensus rule that determines the block reward. The initial reward was 50 BTC which is halved every 210,000 blocks. This results in a total of 21 million bitcoin issued before the reward is zero. Every Bitcoin node can follow this rule or not. The ones that do are in consensus, and will continue to send and receive new, valid transactions between each other. The rule set is arbitrary and it's ultimately up to each individual node operator to decide which rules are followed (typically by choosing to run a specific version of the software). This decision determines if the node will be recognized as a valid network participant, a potentially dire consequence.</p>

    <p>One or more users can decide to run a node with different rules. However, because there is no centralized control over Bitcoin this decision cannot be forced on the entire network. The primary functions nodes perform are creating new transactions and validating transactions from other nodes (making sure transactions follow the rules). Nodes that produce transactions that follow different rules, for example one that increases the block reward and thus the total number of bitcoin beyond 21 million, are excluded from the rest of the network, making them useless.</p>

    <p>Any attempt to “fork” the network by changing consensus rules, especially one that is widely considered essential to the value proposition of Bitcoin like the supply cap, would result in a small minority network that must then compete with the established incumbent. History has proven the chance that such a fork will overtake and supplant the current consensus rules is negligible, and is fast approaching zero as the network continues to grow.</p>
</div>
